<?php

namespace App\Requests\Api\Password;

use App\Requests\Api\BaseApiRequest;
use App\Rules\{
    MailRfc,
    MaxLength,
};

class ForgotRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'email_address' => [
                'required',
                new MailRfc(),
                new MaxLength(255),
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
            'email_address' => 'email_address',
        ];
    }
}
