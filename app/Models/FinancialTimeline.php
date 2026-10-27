<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'name',
    'slug',
    'description',
    'income_multiplier',
    'expense_multiplier',
    'lump_sum_inflow',
    'lump_sum_outflow',
    'monthly_investment_rate',
    'market_cagr',
    'target_horizon_years',
    'is_active_preview',
    'status',
    'merged_at',
])]
class FinancialTimeline extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'income_multiplier',
        'expense_multiplier',
        'lump_sum_inflow',
        'lump_sum_outflow',
        'monthly_investment_rate',
        'market_cagr',
        'target_horizon_years',
        'is_active_preview',
        'status',
        'merged_at',
    ];

    protected $casts = [
        'income_multiplier'       => 'decimal:2',
        'expense_multiplier'      => 'decimal:2',
        'lump_sum_inflow'         => 'decimal:2',
        'lump_sum_outflow'        => 'decimal:2',
        'monthly_investment_rate' => 'decimal:2',
        'market_cagr'             => 'decimal:2',
        'target_horizon_years'    => 'integer',
        'is_active_preview'       => 'boolean',
        'merged_at'               => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Project month-by-month / year-by-year trajectory based on user's financial base.
     */
    public function projectTrajectory(float $currentNetWorth, float $monthlyIncome, float $monthlyExpense): array
    {
        $years = max(5, min(30, (int) $this->target_horizon_years));
        $inflationRate = 0.055; // 5.5% annual inflation
        $cagr = (float) $this->market_cagr / 100.0;
        $investRate = (float) $this->monthly_investment_rate / 100.0;

        $projectedYears = [];
        $netWorth = max(0, $currentNetWorth);
        $portfolio = max(0, $currentNetWorth * 0.70); // Assume 70% in invested assets
        $liquidCash = max(0, $currentNetWorth * 0.30);

        // Apply lump sum shocks in Year 1
        $netWorth += ((float) $this->lump_sum_inflow - (float) $this->lump_sum_outflow);
        $liquidCash += ((float) $this->lump_sum_inflow - (float) $this->lump_sum_outflow);

        $freedomYear = null;
        $startingAge = 28; // User default baseline age

        for ($y = 0; $y <= $years; $y++) {
            $annualInflationFactor = pow(1 + $inflationRate, $y);
            $annualExpense = ($monthlyExpense * (float) $this->expense_multiplier * 12) * $annualInflationFactor;
            $fireThreshold = $annualExpense * 25; // 4% rule

            if ($y === 0) {
                $projectedYears[] = [
                    'year'         => 0,
                    'age'          => $startingAge,
                    'net_worth'    => round($netWorth),
                    'portfolio'    => round($portfolio),
                    'liquid_cash'  => round($liquidCash),
                    'annual_burn'  => round($annualExpense),
                    'fire_target'  => round($fireThreshold),
                    'is_free'      => $netWorth >= $fireThreshold,
                ];
                continue;
            }

            // Simulate monthly steps for this year
            for ($m = 1; $m <= 12; $m++) {
                $mIncome = ($monthlyIncome * (float) $this->income_multiplier) * pow(1 + 0.04, $y); // 4% annual wage bump
                $mExpense = ($monthlyExpense * (float) $this->expense_multiplier) * pow(1 + $inflationRate, $y);
                $surplus = $mIncome - $mExpense;

                if ($surplus > 0) {
                    $toInvest = $surplus * $investRate;
                    $toCash = $surplus * (1 - $investRate);
                    $portfolio += $toInvest;
                    $liquidCash += $toCash;
                } else {
                    // Deficit draws from cash first, then portfolio
                    $liquidCash += $surplus;
                    if ($liquidCash < 0) {
                        $portfolio += $liquidCash;
                        $liquidCash = 0;
                    }
                }

                // Monthly compound growth on portfolio
                $portfolio *= (1 + ($cagr / 12));
            }

            $netWorth = max(0, $portfolio + $liquidCash);

            $isFree = $netWorth >= $fireThreshold;
            if ($isFree && $freedomYear === null) {
                $freedomYear = $y;
            }

            $projectedYears[] = [
                'year'         => $y,
                'age'          => $startingAge + $y,
                'net_worth'    => round($netWorth),
                'portfolio'    => round($portfolio),
                'liquid_cash'  => round($liquidCash),
                'annual_burn'  => round($annualExpense),
                'fire_target'  => round($fireThreshold),
                'is_free'      => $isFree,
            ];
        }

        return [
            'timeline_name' => $this->name,
            'slug'          => $this->slug,
            'years'         => $projectedYears,
            'final_nw'      => end($projectedYears)['net_worth'] ?? 0,
            'freedom_year'  => $freedomYear,
            'freedom_age'   => $freedomYear !== null ? ($startingAge + $freedomYear) : null,
        ];
    }
}
