<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFlockSaleRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sale_date' => ['required', 'date'],
            'house_id' => ['required', 'exists:houses,id'],
            'birds_sold' => ['required', 'integer', 'min:1'],
            'total_weight' => ['required', 'numeric', 'min:0.01'],
            'price_per_kg' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'sale_date' => 'วันที่จับขาย',
            'house_id' => 'เล้า',
            'birds_sold' => 'จำนวนไก่จับขาย',
            'total_weight' => 'น้ำหนักรวม',
            'price_per_kg' => 'ราคาขายต่อกก.',
            'note' => 'หมายเหตุ',
        ];
    }
}
