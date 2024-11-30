<?php

namespace App\Requests\Api\Password;

use App\Libs\ConfigUtil;
use App\Requests\Api\BaseApiRequest;
use App\Rules\{
    MaxLength,
    MinLength,
    ValidPassword,
};

class ResetRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'password' => [
                'required',
                new MinLength(8),
                new MaxLength(16),
                new ValidPassword(),
            ],
            'password_confirmation' => [
                'required',
                new ValidPassword(),
                'same:password',
            ],
            'password_token' => 'required',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes() {
        return [
            'password' => 'password',
            'password_confirmation' => 'password_confirmation',
            'password_token' => 'password_token',
        ];
    }

    /**
     * Validation error messages.
     *
     * @return array
     */
    public function messages() {
        $parrentMessage = parent::messages();

        return array_merge($parrentMessage, [
            'password_confirmation.same' => ConfigUtil::getMessage('ECL030'),
        ]);
    }
}
