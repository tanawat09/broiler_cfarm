<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'house_no',
        'house_name',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function dailyHouseRecords(): HasMany
    {
        return $this->hasMany(DailyHouseRecord::class);
    }

    public function flockHouseStarts(): HasMany
    {
        return $this->hasMany(FlockHouseStart::class);
    }

    public function feedReceiptItems(): HasMany
    {
        return $this->hasMany(FeedReceiptHouseItem::class);
    }

    public function flockSaleRecords(): HasMany
    {
        return $this->hasMany(FlockSaleRecord::class);
    }
}
