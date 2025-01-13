<?php

namespace App\Requests\Api\User;

use App\Libs\ConfigUtil;
use App\Repositories\UserRoleRepository;
use App\Requests\Api\BaseApiRequest;
use App\Rules\{
    MaxLength,
    MinLength,
    Numeric,
    ValidPassword,
    ValidUserRole,
};
use Illuminate\Http\Request;

class EditRequest extends BaseApiRequest
{
    public function __construct(
        private UserRoleRepository $userRoleRepository,
    ) {
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request) {
        $rules = [
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
            'user_role_id' => [
                'required',
            ],
        ];

        if ($request->has('user_role_id') && $request->user_role_id) {
            $rules['user_role_id'] = [
                new Numeric(),
                new ValidUserRole($this->userRoleRepository, $request->user_role_id),
            ];
        }

        return $rules;
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
        $parentMessage = parent::messages();

        return array_merge($parentMessage, [
            'password_confirmation.same' => ConfigUtil::getMessage('ECL030'),
        ]);
    }
}
