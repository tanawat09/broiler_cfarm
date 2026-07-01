<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'role' => ['required', Rule::in([User::ROLE_SUPER_ADMIN, User::ROLE_FARM_MANAGER])],
            'farm_id' => ['nullable', 'required_if:role,'.User::ROLE_FARM_MANAGER, 'exists:farms,id'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'ชื่อผู้ใช้',
            'email' => 'อีเมล',
            'role' => 'สิทธิ์',
            'farm_id' => 'ฟาร์ม',
            'password' => 'รหัสผ่าน',
        ];
    }
}
