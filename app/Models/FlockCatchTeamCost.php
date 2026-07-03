<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlockCatchTeamCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'flock_id',
        'catching_team',
        'fuel_cost',
        'forklift_cost',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'fuel_cost' => 'decimal:2',
            'forklift_cost' => 'decimal:2',
        ];
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function flock(): BelongsTo
    {
        return $this->belongsTo(Flock::class);
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
