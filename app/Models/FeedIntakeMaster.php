<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedIntakeMaster extends Model
{
    use HasFactory;

    protected $fillable = [
        'age',
        'feed_ah',
        'feed_male',
        'feed_female',
    ];

    protected function casts(): array
    {
        return [
            'age' => 'integer',
            'feed_ah' => 'decimal:2',
            'feed_male' => 'decimal:2',
            'feed_female' => 'decimal:2',
        ];
    }
}
