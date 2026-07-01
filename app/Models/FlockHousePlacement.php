<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlockHousePlacement extends Model
{
    use HasFactory;

    protected $fillable = [
        'flock_id',
        'house_id',
        'placement_date',
        'catch_date',
        'catch_age',
        'chicks_in',
        'male_count',
        'female_count',
        'male_grade_a_count',
        'male_grade_b_count',
        'female_grade_a_count',
        'female_grade_b_count',
        'amount',
        'chick_source',
        'chick_grade',
        'chick_code',
        'batch_no',
        'sex',
        'breed',
    ];

    protected function casts(): array
    {
        return [
            'placement_date' => 'date',
            'catch_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function flock(): BelongsTo
    {
        return $this->belongsTo(Flock::class);
    }

    public function house(): BelongsTo
    {
        return $this->belongsTo(House::class);
    }
}
