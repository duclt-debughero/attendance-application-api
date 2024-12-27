<?php

namespace App\Requests\Api\User;

use App\Libs\ConfigUtil;
use App\Repositories\{
    MstUserRepository,
    UserRoleRepository,
};
use App\Requests\Api\BaseApiRequest;
use App\Rules\{
    MailRfc,
    MaxLength,
    MinLength,
    UniqueUser,
    ValidPassword,
    ValidUserRole,
};
use Illuminate\Http\Request;

class AddRequest extends BaseApiRequest
{
    public function __construct(
        private MstUserRepository $mstUserRepository,
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
            'email_address' => [
                'required',
                new MailRfc(),
                new MaxLength(255),
            ],
            'password' => [
                'required',
                new MinLength(8),
                new MaxLength(16),
                new ValidPassword(),
                'different:email_address',
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

        if ($request->has('email_address') && $request->email_address) {
            $rules['email_address'][] = new UniqueUser($this->mstUserRepository, $request->email_address);
        }

        if ($request->has('user_role_id') && $request->user_role_id) {
            $rules['user_role_id'][] = new ValidUserRole($this->userRoleRepository, $request->user_role_id);
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
            'email_address' => 'email_address',
            'password' => 'password',
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
            'password.different' => ConfigUtil::getMessage('ECL029'),
        ]);
    }
}
