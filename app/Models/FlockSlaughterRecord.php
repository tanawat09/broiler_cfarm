<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlockSlaughterRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'flock_id',
        'house_id',
        'slaughter_date',
        'sequence',
        'raw_house_name',
        'slaughter_birds',
        'actual_weight',
        'doa_birds',
        'net_birds',
        'condemned_birds',
        'condemned_percent',
        'problem_birds',
        'problem_percent',
        'dead_weight',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'slaughter_date' => 'date',
            'sequence' => 'integer',
            'slaughter_birds' => 'integer',
            'actual_weight' => 'decimal:2',
            'doa_birds' => 'integer',
            'net_birds' => 'integer',
            'condemned_birds' => 'integer',
            'condemned_percent' => 'decimal:2',
            'problem_birds' => 'integer',
            'problem_percent' => 'decimal:2',
            'dead_weight' => 'decimal:2',
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
