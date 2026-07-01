<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in([User::ROLE_SUPER_ADMIN, User::ROLE_FARM_MANAGER])],
            'farm_id' => ['nullable', 'required_if:role,'.User::ROLE_FARM_MANAGER, 'exists:farms,id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
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
