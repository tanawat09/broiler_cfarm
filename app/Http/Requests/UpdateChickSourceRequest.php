<?php

namespace App\Http\Requests;

use App\Models\ChickSource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateChickSourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        /** @var ChickSource $chickSource */
        $chickSource = $this->route('chick_source');

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('chick_sources', 'name')->ignore($chickSource)],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'ชื่อแหล่งลูกไก่',
            'is_active' => 'สถานะใช้งาน',
        ];
    }
}
