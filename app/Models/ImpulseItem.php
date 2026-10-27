<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'item_name', 'price', 'category', 'cooldown_until', 'status', 'notes'])]
class ImpulseItem extends Model
{
    protected $fillable = [
        'user_id',
        'item_name',
        'price',
        'category',
        'cooldown_until',
        'status',
        'notes',
    ];

    protected $casts = [
        'price'          => 'decimal:2',
        'cooldown_until' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return Carbon::now()->greaterThanOrEqualTo($this->cooldown_until);
    }

    public function hoursRemaining(): int
    {
        if ($this->isExpired()) {
            return 0;
        }

        return max(0, Carbon::now()->diffInHours($this->cooldown_until));
    }

    public function minutesRemaining(): int
    {
        if ($this->isExpired()) {
            return 0;
        }

        return max(0, Carbon::now()->diffInMinutes($this->cooldown_until) % 60);
    }

    /**
     * Hours of labor required based on monthly income (assuming 22 working days * 8 hours = 176 hrs).
     */
    public function lifeEnergyHours(float $monthlyIncome = 50000): float
    {
        $hourlyWage = max(100, $monthlyIncome / 176);
        return round($this->price / $hourlyWage, 1);
    }

    /**
     * Estimated future value after 10 years compounding in Nifty 50 @ 12% CAGR.
     */
    public function opportunityCost10y(): float
    {
        return round($this->price * pow(1 + 0.12, 10), 2);
    }
}
