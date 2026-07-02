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
            'cum_feed_ah' => ['nullable', 'numeric', 'min:0'],
            'cum_feed_male' => ['nullable', 'numeric', 'min:0'],
            'cum_feed_female' => ['nullable', 'numeric', 'min:0'],
            'weight_ah' => ['nullable', 'numeric', 'min:0'],
            'weight_male' => ['nullable', 'numeric', 'min:0'],
            'weight_female' => ['nullable', 'numeric', 'min:0'],
            'mortality_ah' => ['nullable', 'numeric', 'min:0'],
            'mortality_male' => ['nullable', 'numeric', 'min:0'],
            'mortality_female' => ['nullable', 'numeric', 'min:0'],
            'fcr_ah' => ['nullable', 'numeric', 'min:0'],
            'fcr_male' => ['nullable', 'numeric', 'min:0'],
            'fcr_female' => ['nullable', 'numeric', 'min:0'],
            'pi_ah' => ['nullable', 'integer', 'min:0'],
            'pi_male' => ['nullable', 'integer', 'min:0'],
            'pi_female' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'age' => 'อายุ (DOC)',
            'feed_ah' => 'อัตราการกินอาหาร AH',
            'feed_male' => 'อัตราการกินอาหารตัวผู้',
            'feed_female' => 'อัตราการกินอาหารตัวเมีย',
            'cum_feed_ah' => 'สะสมอาหาร AH',
            'cum_feed_male' => 'สะสมอาหารตัวผู้',
            'cum_feed_female' => 'สะสมอาหารตัวเมีย',
            'weight_ah' => 'น้ำหนักตัว AH',
            'weight_male' => 'น้ำหนักตัวตัวผู้',
            'weight_female' => 'น้ำหนักตัวตัวเมีย',
            'mortality_ah' => 'อัตราตายสะสม AH',
            'mortality_male' => 'อัตราตายสะสมตัวผู้',
            'mortality_female' => 'อัตราตายสะสมตัวเมีย',
            'fcr_ah' => 'FCR AH',
            'fcr_male' => 'FCR ตัวผู้',
            'fcr_female' => 'FCR ตัวเมีย',
            'pi_ah' => 'Performance Index AH',
            'pi_male' => 'Performance Index ตัวผู้',
            'pi_female' => 'Performance Index ตัวเมีย',
        ];
    }
}
