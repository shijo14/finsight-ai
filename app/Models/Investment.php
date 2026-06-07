<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['investment_type', 'name', 'amount_invested', 'current_value', 'investment_date'])]
class Investment extends Model
{
    protected $casts = [
        'amount_invested' => 'decimal:2',
        'current_value' => 'decimal:2',
        'investment_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
