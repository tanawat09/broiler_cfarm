<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeedPriceMasterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'feed_code' => ['required', 'string'],
            'price_per_kg' => ['required', 'numeric', 'min:0'],
            'effective_date' => ['required', 'date'],
            'is_active' => ['required', 'boolean'],
            'note' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'feed_code' => 'รหัสอาหาร',
            'price_per_kg' => 'ราคาต่อกก.',
            'effective_date' => 'วันที่เริ่มใช้ราคา',
            'is_active' => 'สถานะใช้งาน',
            'note' => 'หมายเหตุ',
        ];
    }
}
