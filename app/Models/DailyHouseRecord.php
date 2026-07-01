<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class DailyHouseRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'flock_id',
        'house_id',
        'record_date',
        'age_day',
        'feed_code',
        'feed_in',
        'feed_used',
        'water_meter_reading',
        'water_used',
        'temp_min',
        'temp_max',
        'humidity',
        'dead_morning',
        'dead_evening',
        'cull_morning',
        'cull_evening',
        'avg_weight',
        'medicine_note',
        'remark',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'record_date' => 'date',
            'feed_in' => 'decimal:2',
            'feed_used' => 'decimal:2',
            'temp_min' => 'decimal:2',
            'temp_max' => 'decimal:2',
            'humidity' => 'decimal:2',
            'avg_weight' => 'decimal:3',
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

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
