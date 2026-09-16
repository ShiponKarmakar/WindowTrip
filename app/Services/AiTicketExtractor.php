<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Format-agnostic ticket extraction via OpenAI.
 *
 * Given the plain text of a ticket PDF (already extracted on-server), asks the
 * model to return normalised fields as JSON. Used as a fallback when the
 * pattern parser can't confidently read a layout. Returns null on any failure
 * so callers can fall back gracefully.
 */
class AiTicketExtractor
{
    public function enabled(): bool
    {
        return ! empty(config('services.openai.key'));
    }

    /**
     * @return array<string,mixed>|null  Same shape as TicketPdfParser data, or null.
     */
    public function extract(string $ticketText): ?array
    {
        if (! $this->enabled() || trim($ticketText) === '') {
            return null;
        }

        // Guard against huge inputs — a ticket is small; cap to keep cost/time low.
        $ticketText = mb_substr($ticketText, 0, 12000);

        try {
            $response = Http::withToken(config('services.openai.key'))
                ->timeout(45)
                ->acceptJson()
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => config('services.openai.ticket_model', 'gpt-4o-mini'),
                    'temperature' => 0,
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [
                        ['role' => 'system', 'content' => $this->systemPrompt()],
                        ['role' => 'user', 'content' => "Extract the flight ticket details from this text:\n\n".$ticketText],
                    ],
                ]);

            if (! $response->successful()) {
                report(new \RuntimeException('OpenAI ticket extraction failed: '.$response->status().' '.$response->body()));

                return null;
            }

            $content = data_get($response->json(), 'choices.0.message.content');
            if (! $content) {
                return null;
            }

            $parsed = json_decode($content, true);
            if (! is_array($parsed)) {
                return null;
            }

            return $this->normalise($parsed);
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
You extract structured flight-ticket data from raw e-ticket text of ANY airline or agency layout.
Return ONLY a JSON object with exactly these keys:

{
  "pnr": string|null,               // booking/reservation/airline PNR (5-8 chars), null if absent
  "booking_ref": string|null,       // agency booking id / reference, null if absent
  "airline": string|null,           // primary carrier name
  "passengers": [
    { "name": string, "type": "adult"|"child"|"infant", "ticket_number": string|null, "seat": string|null }
  ],
  "segments": [
    {
      "airline": string|null,
      "flight_number": string|null,      // e.g. "2A445" or "EK585"
      "cabin": string|null,              // e.g. "Economy"
      "from_code": string|null,          // 3-letter IATA, uppercase
      "from_city": string|null,
      "to_code": string|null,            // 3-letter IATA, uppercase
      "to_city": string|null,
      "depart_at": string|null,          // "YYYY-MM-DDTHH:MM" 24h local time, null if unknown
      "arrive_at": string|null,          // "YYYY-MM-DDTHH:MM" 24h local time, null if unknown
      "baggage": string|null,            // CHECK-IN baggage e.g. "20kg"
      "cabin_baggage": string|null       // CABIN/carry-on baggage e.g. "7kg"
    }
  ]
}

Rules:
- Strip name titles (Mr/Mrs/Ms/Miss). Uppercase IATA codes.
- Convert dates/times to 24h "YYYY-MM-DDTHH:MM". If only a date is known, use "00:00".
- Do NOT invent values. Use null / empty arrays when not present.
- Output valid JSON only, no commentary.
PROMPT;
    }

    /** Coerce the model output into the exact shape the form expects. */
    private function normalise(array $d): array
    {
        $str = fn ($v) => is_string($v) && trim($v) !== '' ? trim($v) : null;

        $passengers = [];
        foreach ((array) ($d['passengers'] ?? []) as $p) {
            if (! is_array($p) || ! $str($p['name'] ?? null)) {
                continue;
            }
            $type = strtolower((string) ($p['type'] ?? 'adult'));
            $passengers[] = [
                'name' => $str($p['name']),
                'type' => in_array($type, ['adult', 'child', 'infant'], true) ? $type : 'adult',
                'ticket_number' => $str($p['ticket_number'] ?? null),
                'seat' => $str($p['seat'] ?? null),
            ];
        }

        $segments = [];
        foreach ((array) ($d['segments'] ?? []) as $s) {
            if (! is_array($s)) {
                continue;
            }
            $segments[] = [
                'airline' => $str($s['airline'] ?? null) ?? '',
                'flight_number' => $str($s['flight_number'] ?? null) ?? '',
                'cabin' => $str($s['cabin'] ?? null) ?? 'Economy',
                'from_code' => strtoupper((string) ($str($s['from_code'] ?? null) ?? '')),
                'from_city' => $str($s['from_city'] ?? null) ?? '',
                'to_code' => strtoupper((string) ($str($s['to_code'] ?? null) ?? '')),
                'to_city' => $str($s['to_city'] ?? null) ?? '',
                'depart_at' => $this->cleanDateTime($str($s['depart_at'] ?? null)),
                'arrive_at' => $this->cleanDateTime($str($s['arrive_at'] ?? null)),
                'baggage' => $str($s['baggage'] ?? null) ?? '',
                'cabin_baggage' => $str($s['cabin_baggage'] ?? null) ?? '',
            ];
        }

        return array_filter([
            'pnr' => $str($d['pnr'] ?? null),
            'booking_ref' => $str($d['booking_ref'] ?? null),
            'airline' => $str($d['airline'] ?? null) ?? ($segments[0]['airline'] ?? null),
            'passengers' => $passengers,
            'segments' => $segments,
        ], fn ($v) => ! is_null($v) && $v !== '' && $v !== []);
    }

    private function cleanDateTime(?string $v): string
    {
        if (! $v) {
            return '';
        }
        // Accept the model's "YYYY-MM-DDTHH:MM"; tolerate a space or seconds.
        if (preg_match('/(\d{4}-\d{2}-\d{2})[T ](\d{2}:\d{2})/', $v, $m)) {
            return $m[1].'T'.$m[2];
        }

        return '';
    }
}
