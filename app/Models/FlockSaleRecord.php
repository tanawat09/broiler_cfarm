<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlockSaleRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'flock_id',
        'house_id',
        'sale_date',
        'birds_sold',
        'total_weight',
        'avg_weight',
        'price_per_kg',
        'total_amount',
        'note',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'sale_date' => 'date',
            'total_weight' => 'decimal:2',
            'avg_weight' => 'decimal:3',
            'price_per_kg' => 'decimal:2',
            'total_amount' => 'decimal:2',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
