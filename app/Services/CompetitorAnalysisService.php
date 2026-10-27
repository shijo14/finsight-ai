<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CompetitorAnalysisService
{
    /**
     * Generate or retrieve a cached AI competitor analysis briefing for the user.
     */
    public function generate(User $user): ?string
    {
        $competitors = $user->competitors()->get();

        if ($competitors->isEmpty()) {
            return null;
        }

        $cacheKey = "competitor_analysis_{$user->id}_" . $competitors->max('updated_at')?->timestamp;

        return Cache::remember($cacheKey, 3600, function () use ($user, $competitors) {
            return $this->buildAnalysis($user, $competitors);
        });
    }

    /**
     * Force-regenerate the analysis (bypass cache).
     */
    public function regenerate(User $user): ?string
    {
        $competitors = $user->competitors()->get();
        if ($competitors->isEmpty()) {
            return null;
        }
        // Clear all competitor_analysis caches for this user
        Cache::forget("competitor_analysis_{$user->id}_" . $competitors->max('updated_at')?->timestamp);
        return $this->generate($user);
    }

    protected function buildAnalysis(User $user, $competitors): string
    {
        $count   = $competitors->count();
        $avgRent = $competitors->whereNotNull('average_rent')->avg('average_rent');
        $avgOcc  = $competitors->whereNotNull('occupancy_rate')->avg('occupancy_rate');

        $competitorList = $competitors->map(fn($c) =>
            "- {$c->building_name} ({$c->name}): Rent Rs " . number_format((float)$c->average_rent, 0) .
            "/mo, Occupancy " . ($c->occupancy_rate ? $c->occupancy_rate . "%" : "N/A") .
            ($c->notes ? ", Notes: {$c->notes}" : "")
        )->implode("\n");

        $context = <<<CONTEXT
Competitor landscape for {$user->name}:
Total competitors tracked: {$count}
Average market rent: Rs {$avgRent}/month
Average market occupancy: {$avgOcc}%

Competitor Details:
{$competitorList}

Please provide a concise 3-4 sentence competitive intelligence briefing covering:
1. Market positioning (how the market looks overall)
2. Key strengths or opportunities to capitalize on
3. Risks or threats to watch out for
All amounts in Indian Rupees (Rs). Be direct and actionable.
CONTEXT;

        $apiKey = config('services.gemini.api_key');

        if ($apiKey) {
            try {
                $apiResponse = Http::timeout(30)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}",
                    [
                        'systemInstruction' => [
                            'parts' => [[
                                'text' => 'You are a sharp real estate market analyst in India. '
                                    . 'Provide a concise, data-driven competitor intelligence briefing. '
                                    . 'Be direct, professional, and actionable. No bullet points — write in flowing prose. '
                                    . 'Limit to 3-4 sentences max.',
                            ]],
                        ],
                        'contents' => [[
                            'parts' => [['text' => $context]],
                        ]],
                    ]
                );

                if ($apiResponse->successful()) {
                    $text = data_get($apiResponse->json(), 'candidates.0.content.parts.0.text');
                    if ($text) {
                        return trim($text);
                    }
                }

                Log::warning('CompetitorAnalysisService: Gemini returned no content.', [
                    'status' => $apiResponse->status(),
                ]);
            } catch (\Throwable $e) {
                Log::error('CompetitorAnalysisService: API error — ' . $e->getMessage());
            }
        }

        // Fallback
        $avgRentFormatted = format_money($avgRent ?? 0, 0);
        $occLabel = $avgOcc ? round($avgOcc, 1) . "%" : "unknown";
        return "You are tracking {$count} competitor building(s) with an average market rent of {$avgRentFormatted}/month and average occupancy of {$occLabel}. "
            . "Monitoring these properties gives you a clear edge in understanding local pricing and demand dynamics. "
            . "Consider benchmarking your own rates against the highest-occupancy competitors to identify pricing opportunities.";
    }
}
