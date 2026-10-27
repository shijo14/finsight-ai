<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReceiptScannerService
{
    /**
     * Scan an uploaded receipt image and extract structured transaction details.
     *
     * @param UploadedFile $file
     * @return array{merchant: string, amount: float, category: string, date: string, description: string, success: bool}
     */
    public function scan(UploadedFile $file): array
    {
        $mimeType = $file->getMimeType();
        $base64Data = base64_encode(file_get_contents($file->getRealPath()));
        $apiKey = config('services.gemini.api_key');

        if ($apiKey) {
            try {
                $prompt = <<<PROMPT
You are an expert AI financial receipt scanner. Analyze this receipt or bill image carefully.
Extract the transaction details and respond ONLY with a raw JSON object (no markdown code blocks, no backticks, no preamble) with exactly these keys:
{
    "merchant": "Store, restaurant, or business name",
    "amount": 123.45,
    "category": "One of: Groceries, Food & Dining, Utilities, Transportation, Entertainment, Healthcare, Shopping, Housing, Software Tools, Other",
    "date": "YYYY-MM-DD format (or current date if not visible)",
    "description": "Short 3-6 word summary of items purchased"
}
PROMPT;

                $response = Http::timeout(25)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}",
                    [
                        'contents' => [
                            [
                                'parts' => [
                                    [
                                        'inline_data' => [
                                            'mime_type' => $mimeType,
                                            'data'      => $base64Data,
                                        ],
                                    ],
                                    [
                                        'text' => $prompt,
                                    ],
                                ],
                            ],
                        ],
                    ]
                );

                if ($response->successful()) {
                    $rawText = data_get($response->json(), 'candidates.0.content.parts.0.text', '');
                    $cleanJson = trim(preg_replace('/^```(?:json)?|```$/m', '', $rawText));
                    $data = json_decode($cleanJson, true);

                    if (is_array($data) && isset($data['amount'])) {
                        return [
                            'merchant'    => $data['merchant'] ?? 'Receipt Purchase',
                            'amount'      => (float) $data['amount'],
                            'category'    => $this->normalizeCategory($data['category'] ?? 'Shopping'),
                            'date'        => !empty($data['date']) ? Carbon::parse($data['date'])->format('Y-m-d') : now()->format('Y-m-d'),
                            'description' => $data['description'] ?? 'Scanned receipt transaction',
                            'success'     => true,
                        ];
                    }
                }

                Log::warning('ReceiptScannerService: Gemini returned unexpected output', [
                    'status' => $response->status(),
                    'body'   => substr($response->body(), 0, 300),
                ]);
            } catch (\Throwable $e) {
                Log::error('ReceiptScannerService: Scan error — ' . $e->getMessage());
            }
        }

        // Fallback placeholder parsing if API is unreachable (e.g. offline sandbox)
        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        return [
            'merchant'    => ucwords(str_replace(['_', '-'], ' ', $filename)),
            'amount'      => 0.00,
            'category'    => 'Shopping',
            'date'        => now()->format('Y-m-d'),
            'description' => 'Scanned document: ' . $file->getClientOriginalName(),
            'success'     => false,
            'message'     => 'Could not connect to Gemini Vision API. Form pre-filled for manual verification.',
        ];
    }

    protected function normalizeCategory(string $category): string
    {
        $valid = [
            'Groceries',
            'Food & Dining',
            'Utilities',
            'Transportation',
            'Entertainment',
            'Healthcare',
            'Shopping',
            'Housing',
            'Software Tools',
            'Other',
        ];

        foreach ($valid as $v) {
            if (strcasecmp($category, $v) === 0 || str_contains(strtolower($category), strtolower($v))) {
                return $v;
            }
        }

        return 'Shopping';
    }
}
