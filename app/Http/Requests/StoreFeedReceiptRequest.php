<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeedReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'farm_id' => ['required', 'exists:farms,id'],
            'receipt_date' => ['required', 'date'],
            'feed_code' => ['required', 'in:203,204,205'],
            'production_lot' => ['required', 'string', 'max:255'],
            'remark' => ['nullable', 'string'],
            'items' => ['required', 'array'],
            'items.*.quantity_kg' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'farm_id' => 'ฟาร์ม',
            'receipt_date' => 'วันที่รับ',
            'feed_code' => 'เบอร์อาหาร',
            'production_lot' => 'เลขที่ผลิต',
            'remark' => 'หมายเหตุ',
            'items' => 'รายการลงเล้า',
            'items.*.quantity_kg' => 'ปริมาณอาหาร',
        ];
    }
}
