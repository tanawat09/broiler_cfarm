<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeedIntakeMasterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'age' => ['required', 'integer', 'min:0', 'max:100'],
            'feed_ah' => ['required', 'numeric', 'min:0'],
            'feed_male' => ['required', 'numeric', 'min:0'],
            'feed_female' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'age' => 'อายุ (DOC)',
            'feed_ah' => 'อัตราการกินอาหาร AH',
            'feed_male' => 'อัตราการกินอาหารตัวผู้',
            'feed_female' => 'อัตราการกินอาหารตัวเมีย',
        ];
    }
}
