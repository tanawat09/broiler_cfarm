<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedReceiptHouseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'feed_receipt_id',
        'house_id',
        'quantity_kg',
    ];

    protected function casts(): array
    {
        return [
            'quantity_kg' => 'decimal:2',
        ];
    }

    public function feedReceipt(): BelongsTo
    {
        return $this->belongsTo(FeedReceipt::class);
    }

    public function house(): BelongsTo
    {
        return $this->belongsTo(House::class);
    }
}
