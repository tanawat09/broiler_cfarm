<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreDailyRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'record_date' => ['required', 'date'],
            'records' => ['required', 'array'],
            'records.*.feed_code' => ['nullable', 'string', 'max:255'],
            'records.*.feed_in' => ['nullable', 'numeric', 'min:0'],
            'records.*.feed_used' => ['nullable', 'numeric', 'min:0'],
            'records.*.water_meter_reading' => ['nullable', 'integer', 'min:0'],
            'records.*.temp_min' => ['nullable', 'numeric'],
            'records.*.temp_max' => ['nullable', 'numeric'],
            'records.*.humidity' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'records.*.dead_morning' => ['nullable', 'integer', 'min:0'],
            'records.*.dead_evening' => ['nullable', 'integer', 'min:0'],
            'records.*.cull_morning' => ['nullable', 'integer', 'min:0'],
            'records.*.cull_evening' => ['nullable', 'integer', 'min:0'],
            'records.*.avg_weight' => ['nullable', 'numeric', 'min:0'],
            'records.*.medicine_note' => ['nullable', 'string'],
            'records.*.remark' => ['nullable', 'string'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                foreach ($this->input('records', []) as $houseId => $record) {
                    $tempMin = $record['temp_min'] ?? null;
                    $tempMax = $record['temp_max'] ?? null;

                    if ($tempMin !== null && $tempMin !== '' && $tempMax !== null && $tempMax !== '' && (float) $tempMin > (float) $tempMax) {
                        $validator->errors()->add("records.$houseId.temp_max", 'อุณหภูมิสูงสุดต้องมากกว่าหรือเท่ากับอุณหภูมิต่ำสุด');
                    }
                }
            },
        ];
    }

    public function attributes(): array
    {
        return [
            'record_date' => 'วันที่',
            'records.*.feed_code' => 'เบอร์อาหาร',
            'records.*.feed_in' => 'อาหารเข้า',
            'records.*.feed_used' => 'อาหารใช้',
            'records.*.water_meter_reading' => 'เลขมิเตอร์น้ำ',
            'records.*.temp_min' => 'อุณหภูมิต่ำสุด',
            'records.*.temp_max' => 'อุณหภูมิสูงสุด',
            'records.*.humidity' => 'ความชื้น',
            'records.*.dead_morning' => 'ไก่ตายเช้า',
            'records.*.dead_evening' => 'ไก่ตายเย็น',
            'records.*.cull_morning' => 'ไก่คัดทิ้งเช้า',
            'records.*.cull_evening' => 'ไก่คัดทิ้งเย็น',
            'records.*.avg_weight' => 'น้ำหนักเฉลี่ย',
            'records.*.medicine_note' => 'ยา/วัคซีน/อาหารเสริม',
            'records.*.remark' => 'หมายเหตุ',
        ];
    }
}
