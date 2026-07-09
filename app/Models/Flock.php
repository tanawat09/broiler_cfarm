<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Flock extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'flock_code',
        'chicken_type',
        'start_date',
        'initial_birds',
        'status',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
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

    public function flockHousePlacements(): HasMany
    {
        return $this->hasMany(FlockHousePlacement::class);
    }

    public function saleRecords(): HasMany
    {
        return $this->hasMany(FlockSaleRecord::class);
    }

    public function catchRecords(): HasMany
    {
        return $this->hasMany(FlockCatchRecord::class);
    }

    public function slaughterRecords(): HasMany
    {
        return $this->hasMany(FlockSlaughterRecord::class);
    }

    public function medicineRecords(): HasMany
    {
        return $this->hasMany(FlockMedicineRecord::class);
    }
}
