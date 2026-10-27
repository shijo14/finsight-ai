<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'item_name',
    'amount',
    'category',
    'user_justification',
    'kenji_argument',
    'chen_argument',
    'minjun_argument',
    'council_vote',
    'final_ruling',
])]
class WarRoomDeliberation extends Model
{
    protected $fillable = [
        'user_id',
        'item_name',
        'amount',
        'category',
        'user_justification',
        'kenji_argument',
        'chen_argument',
        'minjun_argument',
        'council_vote',
        'final_ruling',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
