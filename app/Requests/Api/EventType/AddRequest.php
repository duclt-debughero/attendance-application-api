<?php

namespace App\Requests\Api\EventType;

use App\Requests\Api\BaseApiRequest;
use App\Rules\MaxLength;

class AddRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'type_name' => [
                'required',
                new MaxLength(150),
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
            'type_name' => 'type_name',
        ];
    }
}
