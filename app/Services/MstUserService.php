<?php

namespace App\Services;

use App\Libs\DateUtil;

class MstUserService
{
    /**
     * Convert data for user detail
     *
     * @param object $user
     * @return mixed
     */
    public function convertDataUserDetail($user) {
        return [
            'user_id' => $user->user_id,
            'email_address' => $user->email_address,
            'user_name' => $user->user_name,
            'telephone_number' => $user->telephone_number,
            'last_login_time' => DateUtil::formatDefaultDateTime($user->last_login_time),
            'user_role' => [
                'user_role_id' => $user->user_role_id,
                'user_role_name' => $user->user_role_name,
            ],
        ];
    }

    /**
     * Convert data for user list
     *
     * @param object $users
     * @return mixed
     */
    public function convertDataUserList($users) {
        $result = [];
        foreach ($users as $user) {
            $result[] = $this->convertDataUserDetail($user);
        }

        return $result;
    }
}
