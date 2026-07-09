<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFlockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'farm_id' => ['required', 'exists:farms,id'],
            'flock_code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('flocks', 'flock_code')
                    ->where(fn ($query) => $query->where('farm_id', $this->input('farm_id'))),
            ],
            'chicken_type' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'note' => ['nullable', 'string'],
            'house_initial_birds' => ['nullable', 'array'],
            'house_initial_birds.*' => ['nullable', 'integer', 'min:0'],
            'house_start_dates' => ['nullable', 'array'],
            'house_start_dates.*' => ['nullable', 'date'],
            'placements' => ['nullable', 'array'],
            'placements.*.placement_date' => ['nullable', 'date'],
            'placements.*.catch_date' => ['nullable', 'date'],
            'placements.*.catch_age' => ['nullable', 'integer', 'min:0'],
            'placements.*.chicks_in' => ['nullable', 'integer', 'min:0'],
            'placements.*.male_count' => ['nullable', 'integer', 'min:0'],
            'placements.*.female_count' => ['nullable', 'integer', 'min:0'],
            'placements.*.male_grade_a_count' => ['nullable', 'integer', 'min:0'],
            'placements.*.male_grade_b_count' => ['nullable', 'integer', 'min:0'],
            'placements.*.female_grade_a_count' => ['nullable', 'integer', 'min:0'],
            'placements.*.female_grade_b_count' => ['nullable', 'integer', 'min:0'],
            'placements.*.chick_source' => ['nullable', 'string', 'max:255'],
            'placements.*.chick_grade' => ['nullable', 'string', 'max:255'],
            'placements.*.chick_code' => ['nullable', 'string'],
            'placements.*.batch_no' => ['nullable', 'string', 'max:255'],
            'placements.*.sex' => ['nullable', 'string', 'max:255'],
            'placements.*.breed' => ['nullable', 'string', 'max:255'],
            'redirect_to' => ['nullable', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'flock_code' => trim((string) $this->input('flock_code')),
            'chicken_type' => trim((string) $this->input('chicken_type')),
        ]);
    }

    public function messages(): array
    {
        return [
            'flock_code.unique' => 'ฟาร์มนี้มีรุ่นการเลี้ยงนี้อยู่แล้ว กรุณาใช้รหัสรุ่นอื่น',
        ];
    }

    public function attributes(): array
    {
        return [
            'farm_id' => 'ฟาร์ม',
            'flock_code' => 'รหัสรุ่น',
            'chicken_type' => 'ประเภทไก่',
            'start_date' => 'วันที่เริ่มเลี้ยง',
            'note' => 'หมายเหตุ',
            'house_initial_birds' => 'จำนวนไก่เริ่มต้นแยกตามเล้า',
            'house_initial_birds.*' => 'จำนวนไก่เริ่มต้น',
            'house_start_dates' => 'วันที่เริ่มเลี้ยงแยกตามเล้า',
            'house_start_dates.*' => 'วันที่เริ่มเลี้ยงของเล้า',
            'placements.*.placement_date' => 'วันที่ลง',
            'placements.*.catch_date' => 'วันที่จับ',
            'placements.*.catch_age' => 'อายุจับ',
            'placements.*.chicks_in' => 'ไก่เข้า',
            'placements.*.male_count' => 'M',
            'placements.*.female_count' => 'FM',
            'placements.*.male_grade_a_count' => 'เกรด M A',
            'placements.*.male_grade_b_count' => 'เกรด M B',
            'placements.*.female_grade_a_count' => 'เกรด FM A',
            'placements.*.female_grade_b_count' => 'เกรด FM B',
            'placements.*.chick_source' => 'แหล่งลูกไก่',
            'placements.*.chick_grade' => 'เกรดลูกไก่',
            'placements.*.chick_code' => 'รหัส',
            'placements.*.batch_no' => 'รุ่น',
            'placements.*.sex' => 'เพศ',
            'placements.*.breed' => 'พันธุ์',
        ];
    }
}
