<?php

namespace App\Requests\Api\User;

use App\Libs\ConfigUtil;
use App\Requests\Api\BaseApiRequest;
use App\Rules\{
    MaxLength,
    MinLength,
    ValidPassword,
};

class EditRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'password' => [
                'nullable',
                new MinLength(8),
                new MaxLength(16),
                new ValidPassword(),
            ],
            'password_confirmation' => [
                'nullable',
                new ValidPassword(),
                'same:password'
            ],
            'user_name' => [
                'required',
                new MaxLength(255),
            ],
            'telephone_number' => [
                'nullable',
                new MaxLength(20),
            ],
            'user_role_id' => 'required',
        ];
    }

    /**
     * Retrieves the attributes of the user model.
     *
     * @return array An associative array with the keys:
     */
    public function attributes() {
        return [
            'password' => 'password',
            'password_confirmation' => 'password_confirmation',
            'user_name' => 'user_name',
            'telephone_number' => 'telephone_number',
            'user_role_id' => 'user_role_id',
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
