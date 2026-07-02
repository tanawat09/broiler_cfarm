<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedPriceMaster extends Model
{
    use HasFactory;

    protected $fillable = [
        'feed_code',
        'price_per_kg',
        'effective_date',
        'is_active',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'price_per_kg' => 'decimal:2',
            'effective_date' => 'date',
            'is_active' => 'boolean',
        ];
    }
}
