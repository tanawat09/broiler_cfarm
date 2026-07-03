<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlockCatchRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'flock_id',
        'house_id',
        'catch_date',
        'sequence',
        'license_plate',
        'birds_count',
        'boxes_count',
        'vehicle_type',
        'catching_team',
        'catching_fee',
        'note',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'catch_date' => 'date',
            'sequence' => 'integer',
            'birds_count' => 'integer',
            'boxes_count' => 'integer',
            'catching_fee' => 'decimal:2',
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
