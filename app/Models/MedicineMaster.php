<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicineMaster extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'default_unit',
        'is_active',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function flockMedicineRecords(): HasMany
    {
        return $this->hasMany(FlockMedicineRecord::class);
    }
}
