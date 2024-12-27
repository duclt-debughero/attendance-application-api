<?php

namespace App\Requests\Api\UserRole;

use App\Requests\Api\BaseApiRequest;
use App\Rules\MaxLength;
use Illuminate\Http\Request;

class AddRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request) {
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
