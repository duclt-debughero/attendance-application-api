<?php

namespace App\Services;

use App\Libs\DateUtil;
use App\Libs\EncryptUtil;

class UserRoleService
{
    /**
     * Convert data for user role detail
     *
     * @param object $userRole
     * @return mixed
     */
    public function convertDataUserRoleDetail($userRole) {
        return [
            'user_role_id' => $userRole->user_role_id,
            'user_role_name' => $userRole->user_role_name,
            'last_updated_by' => EncryptUtil::decryptAes256($userRole->last_updated_by),
            'last_updated_at' => DateUtil::formatDefaultDateTime($userRole->last_updated_at),
        ];
    }

    /**
     * Convert data for user role list
     *
     * @param object $userRoles
     * @return mixed
     */
    public function convertDataUserRoleList($userRoles) {
        $result = [];
        foreach ($userRoles as $userRole) {
            $result[] = $this->convertDataUserRoleDetail($userRole);
        }

        return $result;
    }
}
