<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class FlockHouseStart extends Model
{
    use HasFactory;

    protected $fillable = [
        'flock_id',
        'house_id',
        'initial_birds',
        'start_date',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
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
