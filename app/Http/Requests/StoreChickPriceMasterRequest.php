<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreChickPriceMasterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sex' => ['required', Rule::in(['ผู้', 'เมีย', 'คละ'])],
            'grade' => ['required', Rule::in(['A', 'B'])],
            'price_per_bird' => ['required', 'numeric', 'min:0'],
            'effective_date' => ['required', 'date'],
            'is_active' => ['required', 'boolean'],
            'note' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'sex' => 'เพศ',
            'grade' => 'เกรด',
            'price_per_bird' => 'ราคาลูกไก่ต่อตัว',
            'effective_date' => 'วันที่เริ่มใช้ราคา',
            'is_active' => 'สถานะใช้งาน',
            'note' => 'หมายเหตุ',
        ];
    }
}
