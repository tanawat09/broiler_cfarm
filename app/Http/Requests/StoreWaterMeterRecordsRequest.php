<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWaterMeterRecordsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'record_date' => ['required', 'date'],
            'meters' => ['required', 'array'],
            'meters.*' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'record_date' => 'วันที่บันทึก',
            'meters' => 'เลขมิเตอร์น้ำ',
            'meters.*' => 'เลขมิเตอร์น้ำ',
        ];
    }
}
