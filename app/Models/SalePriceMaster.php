<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalePriceMaster extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
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

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }
}
