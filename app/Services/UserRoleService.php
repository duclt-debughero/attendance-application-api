<?php

namespace App\Services;

use App\Libs\DateUtil;
use App\Libs\EncryptUtil;

class UserRoleService
{
    /**
     * Convert data for user role
     *
     * @param object $userRoles
     * @return mixed
     */
    public function convertDataUserRole($userRoles) {
        $result = [];

        foreach ($userRoles as $userRole) {
            $userRoleId = $userRole->user_role_id;

            // Initialize the user role if it doesn't exist
            if (! isset($result[$userRoleId])) {
                $result[$userRoleId] = [
                    'user_role_id' => $userRoleId,
                    'user_role_name' => $userRole->user_role_name,
                    'last_updated_by' => EncryptUtil::decryptAes256($userRole->last_updated_by),
                    'last_updated_at' => DateUtil::formatDefaultDateTime($userRole->last_updated_at),
                    'role_menu' => []
                ];
            }

            // Add menu information if available
            if (isset($userRole->menu_id)) {
                $result[$userRoleId]['role_menu'][] = [
                    'menu_id' => $userRole->menu_id,
                    'menu_name' => $userRole->menu_name,
                    'permission_id' => $userRole->permission_id,
                    'permission_type' => $userRole->permission_type,
                ];
            }
        }

        // Sort the role_menu for each user_role_id
        foreach ($result as &$userRoleData) {
            usort($userRoleData['role_menu'], function ($menuA, $menuB) {
                return $menuA['menu_id'] <=> $menuB['menu_id'];
            });
        }

        return array_values($result);
    }
}
