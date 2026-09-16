<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Smalot\PdfParser\Parser;

/**
 * Best-effort extraction of flight-ticket fields from a text-based PDF.
 *
 * Handles a couple of common Bangladeshi agency/GDS e-ticket layouts, but is
 * deliberately tolerant: anything it can't find is left out for the admin to
 * fill in. Scanned/image PDFs have no text layer and yield an empty result.
 *
 * NOTE: this is pattern-based, so each new vendor layout may need tuning.
 * See parse() callers for the AI-assisted path when that is enabled.
 */
class TicketPdfParser
{
    public function parse(string $path): array
    {
        $text = $this->extractText($path);

        if (trim($text) === '') {
            return ['ok' => false, 'reason' => 'no_text', 'data' => []];
        }

        // Convert non-breaking / narrow spaces to normal spaces first, otherwise
        // \s in the field patterns won't match them.
        $text = str_replace(["\xc2\xa0", "\xe2\x80\xaf"], ' ', $text);
        $lines = preg_split('/\r\n|\r|\n/', $text);
        $lines = array_map(fn ($l) => trim(preg_replace('/[\pZ\t]+/u', ' ', $l)), $lines);
        $flat = implode("\n", $lines);

        $data = [
            'pnr' => $this->pnr($flat),
            'booking_ref' => $this->bookingRef($flat),
        ];

        $data['passengers'] = $this->parsePassengers($lines, $flat);
        $data['segments'] = $this->parseSegments($flat);
        $data['airline'] = $data['segments'][0]['airline'] ?? null;

        $data = array_filter($data, fn ($v) => ! is_null($v) && $v !== '' && $v !== []);

        $found = ! empty($data['pnr']) || ! empty($data['segments']) || ! empty($data['passengers']);

        return ['ok' => $found, 'reason' => $found ? null : 'unrecognised', 'data' => $data];
    }

    private function extractText(string $path): string
    {
        try {
            return (new Parser())->parseFile($path)->getText();
        } catch (\Throwable $e) {
            report($e);

            return '';
        }
    }

    /** @return array<int,array<string,string>> */
    private function parsePassengers(array $lines, string $flat): array
    {
        // Strategy A: one passenger per line, e.g.
        // "Mr SAIKAT SAIKAT Adult → Male X84S4S X84S4S"
        $out = [];
        foreach ($lines as $line) {
            if (! preg_match('/\b(Adult|Child|Infant)\b/i', $line) || stripos($line, 'Passenger Name') !== false) {
                continue;
            }
            if (! preg_match('/^(?:Mr|Mrs|Ms|Miss|Master|Mstr|Dr)?\.?\s*(.+?)\s+(Adult|Child|Infant)\b/i', $line, $m)) {
                continue;
            }
            $name = trim($m[1]);
            if ($name === '' || mb_strlen($name) > 60) {
                continue;
            }
            $ticket = null;
            if (preg_match('/(?:Adult|Child|Infant)[^A-Z0-9]*(?:→\s*\w+)?\s*([A-Z0-9]{5,})(?:\s+([A-Z0-9]{5,}))?\s*$/i', $line, $t)) {
                $ticket = $t[2] ?? $t[1] ?? null;
            }
            $out[] = ['name' => $this->tidyName($name), 'type' => strtolower($m[2]), 'ticket_number' => $ticket, 'seat' => ''];
        }
        if (! empty($out)) {
            return $out;
        }

        // Strategy B: run-together table, e.g.
        // "MS BILKIS AKTERADULT610241649280220KG7Kg"
        if (preg_match_all('/\b(MR|MRS|MS|MISS|MSTR|MASTER|DR)\.?\s+([A-Z][A-Z .\'\-]*?)(ADULT|CHILD|INFANT)(\d{13})?/u', $flat, $mm, PREG_SET_ORDER)) {
            foreach ($mm as $m) {
                $name = trim($m[2]);
                if ($name === '' || mb_strlen($name) > 60) {
                    continue;
                }
                $out[] = ['name' => $name, 'type' => strtolower($m[3]), 'ticket_number' => $m[4] ?? null, 'seat' => ''];
            }
        }

        return $out;
    }

    /** @return array<int,array<string,string>> */
    private function parseSegments(string $flat): array
    {
        $arrow = $this->parseSegmentsArrow($flat);

        return ! empty($arrow) ? $arrow : $this->parseSegmentsPaired($flat);
    }

    /** Layout with "City(XXX) → City(YYY)" route headers + "Departs/Arrival" lines. */
    private function parseSegmentsArrow(string $flat): array
    {
        $pattern = '/([A-Za-z .\'-]+)\(([A-Z]{3})\)\s*→\s*([A-Za-z .\'-]+)\(([A-Z]{3})\)/u';
        if (! preg_match_all($pattern, $flat, $heads, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
            return [];
        }

        $segments = [];
        $count = count($heads);
        foreach ($heads as $idx => $h) {
            $start = $h[0][1];
            $end = ($idx + 1 < $count) ? $heads[$idx + 1][0][1] : strlen($flat);
            $chunk = substr($flat, $start, $end - $start);

            $airline = $this->match('/\n?\s*([A-Za-z][A-Za-z0-9 .\'-]*?)\s*\|\s*Flight No/i', $chunk);
            $flightNo = $this->match('/Flight No\s*[-:]?\s*([A-Z0-9]{1,6})/i', $chunk);
            $cabin = $this->match('/\b(Economy|Premium Economy|Business|First)\b/i', $chunk);
            $baggage = $this->match('/([\d]+(?:\.\d+)?)\s*KG/i', $chunk);

            [$depDate, $depTime] = $this->matchDateTime('/([0-9]{1,2}\s+[A-Za-z]{3,9},?\s+[0-9]{4})\s+([0-9]{1,2}:[0-9]{2})\s*Departs/i', $chunk);
            [$arrDate, $arrTime] = $this->matchDateTime('/([0-9]{1,2}\s+[A-Za-z]{3,9},?\s+[0-9]{4})\s+([0-9]{1,2}:[0-9]{2})\s*Arriv/i', $chunk);

            $segments[] = [
                'airline' => $airline ? trim($airline) : '',
                'flight_number' => $flightNo ? strtoupper($flightNo) : '',
                'cabin' => $cabin ? ucwords(strtolower($cabin)) : 'Economy',
                'from_city' => trim($h[1][0]),
                'from_code' => strtoupper($h[2][0]),
                'to_city' => trim($h[3][0]),
                'to_code' => strtoupper($h[4][0]),
                'depart_at' => $this->toLocal($depDate, $depTime),
                'arrive_at' => $this->toLocal($arrDate, $arrTime),
                'baggage' => $this->normBaggage($baggage),
            ];
        }

        return $segments;
    }

    /**
     * Layout with detailed stop lines: "City (XXX)Day DD Mon YYYY, HH:MM".
     * Consecutive stops pair up as departure -> arrival per flight.
     */
    private function parseSegmentsPaired(string $flat): array
    {
        // City may run straight into the previous field, so keep the city class
        // tight (letters/space/apostrophe/dot, no hyphens) to avoid dash noise.
        $stopRe = '/([A-Za-z][A-Za-z .\']*?)\s*\(([A-Z]{3})\)\s*(?:[A-Za-z]{3,9}\s+)?(\d{1,2}\s+[A-Za-z]{3,9}\s+\d{4}),?\s*(\d{1,2}:\d{2})/u';
        if (! preg_match_all($stopRe, $flat, $stops, PREG_SET_ORDER) || count($stops) < 2) {
            return [];
        }

        // Airline + flight number, e.g. "Economy ClassAir Astra | 2A - 445 | ATR 72".
        preg_match_all('/(?:Economy|Business|First|Premium)\s*Class\s*([A-Za-z][A-Za-z .\'\-]*?)\s*\|\s*([A-Z0-9]{2})\s*[-\s]\s*(\d{1,4})/u', $flat, $fm, PREG_SET_ORDER);
        $cabin = $this->match('/\b(Economy|Business|First|Premium)\s*Class/i', $flat) ?: 'Economy';
        // Baggage: 1-2 digits before KG, not part of a longer digit run (avoids
        // grabbing digits off a jammed e-ticket number).
        $baggage = $this->normBaggage($this->match('/(?<!\d)(\d{1,2})\s*KG\b/i', $flat));

        $segments = [];
        $pairs = intdiv(count($stops), 2);
        for ($k = 0; $k < $pairs; $k++) {
            $dep = $stops[2 * $k];
            $arr = $stops[2 * $k + 1];
            $airline = isset($fm[$k][1]) ? trim($fm[$k][1]) : '';
            $flight = isset($fm[$k]) ? strtoupper($fm[$k][2].$fm[$k][3]) : '';

            $segments[] = [
                'airline' => $airline,
                'flight_number' => $flight,
                'cabin' => ucwords(strtolower($cabin)),
                'from_city' => trim($dep[1]),
                'from_code' => strtoupper($dep[2]),
                'to_city' => trim($arr[1]),
                'to_code' => strtoupper($arr[2]),
                'depart_at' => $this->toLocal($dep[3], $dep[4]),
                'arrive_at' => $this->toLocal($arr[3], $arr[4]),
                'baggage' => $baggage,
            ];
        }

        return $segments;
    }

    /**
     * PNR — labelled value that may be jammed against the next field (no space).
     * Capture is case-sensitive (upper+digits) and stops at a lowercase letter,
     * space, or a following label like "Booking"/"Confirmed".
     */
    private function pnr(string $flat): ?string
    {
        foreach (['Reservation PNR', 'Airline PNR', 'GDS PNR', 'PNR'] as $label) {
            $re = '/(?i:'.preg_quote($label, '/').')\s*:?\s*([A-Z0-9]{5,8}?)(?=Booking|Confirm|[a-z]|[\s\n]|$)/';
            if (preg_match($re, $flat, $m)) {
                return $m[1];
            }
        }

        return null;
    }

    private function bookingRef(string $flat): ?string
    {
        $re = '/(?i:Booking\s*Id)\s*:?\s*([A-Z0-9]{6,}?)(?=Confirm|CONFIRM|[a-z]|[\s\n]|$)/';

        return preg_match($re, $flat, $m) ? $m[1] : null;
    }

    private function match(string $re, string $subject): ?string
    {
        return preg_match($re, $subject, $m) ? trim($m[1]) : null;
    }

    /** @return array{0:?string,1:?string} */
    private function matchDateTime(string $re, string $subject): array
    {
        return preg_match($re, $subject, $m) ? [$m[1], $m[2]] : [null, null];
    }

    private function normBaggage(?string $kg): string
    {
        if (! $kg) {
            return '';
        }

        return rtrim(rtrim($kg, '0'), '.').'kg';
    }

    private function toLocal(?string $date, ?string $time): string
    {
        if (! $date || ! $time) {
            return '';
        }
        try {
            return Carbon::parse(str_replace(',', '', $date).' '.$time)->format('Y-m-d\TH:i');
        } catch (\Throwable $e) {
            return '';
        }
    }

    private function tidyName(string $name): string
    {
        $name = preg_replace('/^(Mr|Mrs|Ms|Miss|Master|Mstr|Dr)\.?\s+/i', '', $name);

        return trim($name);
    }
}
