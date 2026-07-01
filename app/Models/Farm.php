<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Farm extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_name',
        'company_name',
        'owner_name',
        'address',
        'farm_code',
        'house_count',
        'rearing_area',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'house_count' => 'integer',
            'rearing_area' => 'integer',
        ];
    }

    public function houses(): HasMany
    {
        return $this->hasMany(House::class);
    }

    public function flocks(): HasMany
    {
        return $this->hasMany(Flock::class);
    }

    public function feedReceipts(): HasMany
    {
        return $this->hasMany(FeedReceipt::class);
    }

    public function salePriceMasters(): HasMany
    {
        return $this->hasMany(SalePriceMaster::class);
    }
}
