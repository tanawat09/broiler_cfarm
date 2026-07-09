<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlockProductionReportAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'flock_id',
        'items',
        'note',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'items' => 'array',
        ];
    }

    public function flock(): BelongsTo
    {
        return $this->belongsTo(Flock::class);
    }
}
