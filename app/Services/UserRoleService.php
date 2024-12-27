<?php

namespace App\Services;

use App\Libs\{
    DateUtil,
    EncryptUtil,
    ValueUtil,
};
use App\Repositories\{
    RoleMenuRepository,
    RolePermissionRepository,
    UserRoleRepository,
};
use Exception;
use Illuminate\Support\Facades\{DB, Log};

class UserRoleService
{
    public function __construct(
        private RoleMenuRepository $roleMenuRepository,
        private RolePermissionRepository $rolePermissionRepository,
        private UserRoleRepository $userRoleRepository,
    ) {
    }

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

    /**
     * Handle create user role
     *
     * @param array $params
     * @return mixed
     */
    public function handleCreateUserRole($params) {
        try {
            // Create user role
            $userRole = $this->userRoleRepository->create(['user_role_name' => $params['user_role_name']]);
            if (empty($userRole)) {
                DB::rollBack();
                return false;
            }

            // Set default permission type
            $defaultPermissionType = ValueUtil::get('role_permission.default_permission_type');
            $permissionTypes = ValueUtil::getList('role_permission.permission_type');

            // Create role permission
            $roleMenus = $this->roleMenuRepository->getAllRoleMenu();
            foreach ($roleMenus as $roleMenu) {
                // Set permission type
                $permissionType = $params['role_permissions'][$roleMenu->menu_id] ?? $defaultPermissionType;
                $permissionType = isset($permissionTypes[$permissionType]) ? $permissionType : $defaultPermissionType;

                $rolePermission = $this->rolePermissionRepository->create([
                    'user_role_id' => $userRole->user_role_id,
                    'menu_id' => $roleMenu->menu_id,
                    'permission_type' => $permissionType,
                ]);
                if (empty($rolePermission)) {
                    DB::rollBack();
                    return false;
                }
            }

            // Get user role by user role id
            $userRole = $this->userRoleRepository->getUserRoleByUserRoleId($userRole->user_role_id);

            DB::commit();
            return $userRole;
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }
}
