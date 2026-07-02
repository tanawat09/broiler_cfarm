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
        'cum_feed_ah',
        'cum_feed_male',
        'cum_feed_female',
        'weight_ah',
        'weight_male',
        'weight_female',
        'mortality_ah',
        'mortality_male',
        'mortality_female',
        'fcr_ah',
        'fcr_male',
        'fcr_female',
        'pi_ah',
        'pi_male',
        'pi_female',
    ];

    protected function casts(): array
    {
        return [
            'age' => 'integer',
            'feed_ah' => 'decimal:2',
            'feed_male' => 'decimal:2',
            'feed_female' => 'decimal:2',
            'cum_feed_ah' => 'decimal:2',
            'cum_feed_male' => 'decimal:2',
            'cum_feed_female' => 'decimal:2',
            'weight_ah' => 'decimal:2',
            'weight_male' => 'decimal:2',
            'weight_female' => 'decimal:2',
            'mortality_ah' => 'decimal:2',
            'mortality_male' => 'decimal:2',
            'mortality_female' => 'decimal:2',
            'fcr_ah' => 'decimal:3',
            'fcr_male' => 'decimal:3',
            'fcr_female' => 'decimal:3',
            'pi_ah' => 'integer',
            'pi_male' => 'integer',
            'pi_female' => 'integer',
        ];
    }
}
