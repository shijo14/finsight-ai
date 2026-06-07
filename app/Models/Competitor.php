<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['name', 'building_name', 'address', 'latitude', 'longitude', 'occupancy_rate', 'average_rent', 'notes'])]
class Competitor extends Model
{
    protected $casts = [
        'latitude' => 'double',
        'longitude' => 'double',
        'occupancy_rate' => 'decimal:2',
        'average_rent' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
