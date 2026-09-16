<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Smalot\PdfParser\Parser;

/**
 * Best-effort extraction of flight-ticket fields from a text-based PDF.
 *
 * Tuned for the agency/GDS e-ticket layout WindowTrip issues, but tolerant:
 * anything it can't find is simply left out for the admin to fill in.
 * Scanned/image PDFs have no text layer and yield an empty result.
 */
class TicketPdfParser
{
    public function parse(string $path): array
    {
        $text = $this->extractText($path);

        if (trim($text) === '') {
            return ['ok' => false, 'reason' => 'no_text', 'data' => []];
        }

        // Normalise whitespace but keep line breaks — they anchor the layout.
        // Convert non-breaking spaces (U+00A0, common in agency PDFs) to real
        // spaces first, otherwise \s in the field patterns won't match them.
        $text = str_replace(["\xc2\xa0", "\xe2\x80\xaf"], ' ', $text);
        $lines = preg_split('/\r\n|\r|\n/', $text);
        $lines = array_map(fn ($l) => trim(preg_replace('/[\pZ\t]+/u', ' ', $l)), $lines);
        $flat = implode("\n", $lines);

        $data = [
            'pnr' => $this->match('/Reservation PNR\s*:?\s*([A-Z0-9]{5,8})/i', $flat)
                ?: $this->match('/\bPNR\s*:?\s*([A-Z0-9]{5,8})/i', $flat),
            'booking_ref' => $this->match('/Booking ID\s*:?\s*([A-Z0-9]{6,})/i', $flat),
        ];

        $data['passengers'] = $this->parsePassengers($lines);
        $data['segments'] = $this->parseSegments($flat);

        // Airline at ticket level = first segment's airline, if any.
        $data['airline'] = $data['segments'][0]['airline'] ?? null;

        // Drop null/empty top-level keys.
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
    private function parsePassengers(array $lines): array
    {
        $passengers = [];
        foreach ($lines as $line) {
            // e.g. "Mr SAIKAT SAIKAT Adult → Male X84S4S X84S4S"
            if (! preg_match('/\b(Adult|Child|Infant)\b/i', $line)) {
                continue;
            }
            if (stripos($line, 'Passenger Name') !== false) {
                continue; // table header
            }

            if (! preg_match('/^(?:Mr|Mrs|Ms|Miss|Master|Mstr|Dr)?\.?\s*(.+?)\s+(Adult|Child|Infant)\b/i', $line, $m)) {
                continue;
            }
            $name = trim($m[1]);
            if ($name === '' || mb_strlen($name) > 60) {
                continue;
            }

            // Trailing tokens after the type/gender often hold "AirlinePNR TicketNo".
            $ticket = null;
            if (preg_match('/(?:Adult|Child|Infant)[^A-Z0-9]*(?:→\s*\w+)?\s*([A-Z0-9]{5,})(?:\s+([A-Z0-9]{5,}))?\s*$/i', $line, $t)) {
                $ticket = $t[2] ?? $t[1] ?? null;
            }

            $passengers[] = [
                'name' => $this->tidyName($name),
                'type' => strtolower($m[2]),
                'ticket_number' => $ticket,
                'seat' => '',
            ];
        }

        return $passengers;
    }

    /** @return array<int,array<string,string>> */
    private function parseSegments(string $flat): array
    {
        // Split into one chunk per "City(XXX) → City(YYY)" route header.
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
            if ($airline && $flightNo && ! preg_match('/[A-Za-z]/', $flightNo)) {
                // Prefix a 2-letter code from the airline when the number is bare digits? Leave as-is.
            }

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
                'baggage' => $baggage ? (rtrim(rtrim($baggage, '0'), '.').'kg') : '',
            ];
        }

        return $segments;
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
