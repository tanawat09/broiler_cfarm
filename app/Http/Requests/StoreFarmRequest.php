<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFarmRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'farm_name' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'owner_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'farm_code' => ['nullable', 'string', 'max:255'],
            'house_count' => ['required', 'integer', 'min:1', 'max:20'],
            'rearing_area' => ['nullable', 'integer', 'min:1'],
            'note' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'farm_name' => 'ชื่อฟาร์ม',
            'company_name' => 'ชื่อบริษัท',
            'owner_name' => 'ชื่อเจ้าของ',
            'address' => 'ที่อยู่',
            'farm_code' => 'รหัสฟาร์ม',
            'house_count' => 'จำนวนเล้า',
            'rearing_area' => 'พื้นที่การเลี้ยง (ตร.ม.)',
            'note' => 'หมายเหตุ',
        ];
    }
}
