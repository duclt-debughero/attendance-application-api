<?php

namespace App\Requests\Api\Auth;

use App\Requests\Api\BaseApiRequest;
use App\Rules\Numeric;

class LogoutRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'device_id' => [
                'required',
                new Numeric(),
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array {
        return [
            'device_id' => 'device_id',
        ];
    }
}
