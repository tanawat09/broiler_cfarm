<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChickPriceMaster extends Model
{
    use HasFactory;

    public const SEXES = ['ผู้', 'เมีย', 'คละ'];
    public const GRADES = ['A', 'B'];

    protected $fillable = [
        'sex',
        'grade',
        'price_per_bird',
        'effective_date',
        'is_active',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'price_per_bird' => 'decimal:2',
            'effective_date' => 'date',
            'is_active' => 'boolean',
        ];
    }
}
