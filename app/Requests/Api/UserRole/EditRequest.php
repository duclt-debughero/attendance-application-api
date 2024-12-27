<?php

namespace App\Requests\Api\UserRole;

use App\Requests\Api\BaseApiRequest;
use App\Rules\MaxLength;

class EditRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'user_role_name' => [
                'required',
                new MaxLength(100),
            ],
        ];
    }

    /**
     * Retrieves the attributes of the user model.
     *
     * @return array An associative array with the keys:
     */
    public function attributes() {
        return [
            'user_role_name' => 'user_role_name',
        ];
    }
}
