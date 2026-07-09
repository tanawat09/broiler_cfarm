<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlockMedicineRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'flock_id',
        'house_id',
        'medicine_master_id',
        'record_date',
        'age_day',
        'medicine_name',
        'quantity',
        'dose_per_1000_birds',
        'unit',
        'note',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'record_date' => 'date',
            'dose_per_1000_birds' => 'decimal:2',
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

    public function medicineMaster(): BelongsTo
    {
        return $this->belongsTo(MedicineMaster::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
