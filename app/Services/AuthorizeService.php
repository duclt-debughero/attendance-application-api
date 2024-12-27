<?php

namespace App\Services;

use App\Libs\ValueUtil;
use App\Repositories\{
    RoleMenuRepository,
    RolePermissionRepository,
};
use UnexpectedValueException;

class AuthorizeService
{
    /**
     * Request attribute name for permission type
     */
    public const REQUEST_ATTRIBUTE_PERMISSION_TYPE = 'permissionType';

    /**
     * Default permission type
     *
     * @var int
     */
    private $defaultPermissionType;

    public function __construct(
        private RolePermissionRepository $rolePermissionRepository,
        private RoleMenuRepository $roleMenuRepository,
    ) {
        $this->defaultPermissionType = ValueUtil::constToValue('role_permission.permission_type.NO_ACCESS');
    }

    /**
     * Get default permission type
     */
    public function getDefaultPermissionType() {
        return $this->defaultPermissionType;
    }

    /**
     * Retrieves the current user's permission type for a specific menu.
     *
     * @param string|int $userRoleId The ID of the user's role.
     * @param string|int $menuId The ID of the menu.
     * @return int The permission type of the current user for the specified menu.
     */
    public function getCurrentUserPermissionType($userRoleId, $menuId) {
        $rolePermission = $this->rolePermissionRepository->getRolePermissionByUserRoleIdAndMenuId($userRoleId, $menuId);

        // If the role permission is not found or invalid, return the default permission type
        if (
            ! isset($rolePermission)
            || ! isset($rolePermission->permission_type)
            || ! $this->isValidPermissionType($rolePermission->permission_type)
        ) {
            return $this->defaultPermissionType;
        }

        return $rolePermission->permission_type;
    }

    /**
     * Checks if the given permission type is valid.
     *
     * @param int $permissionType The permission type to be validated.
     * @return bool True if the permission type is valid, false otherwise.
     */
    public function isValidPermissionType($permissionType) {
        $permissionTypes = ValueUtil::getList('role_permission.permission_type');

        return isset($permissionTypes[$permissionType]);
    }

    /**
     * Retrieves a list of role permission menus for a given user role id.
     *
     * @param string|int $userRoleId The ID of the user role.
     * @return mixed A collection of role permission menus or false on failure.
     */
    public function getRolePermissionMenus($userRoleId) {
        return $this->roleMenuRepository->getRolePermissionMenus($userRoleId);
    }

    /**
     * Filters the list of role permission menus to only include those with permission types that allow showing.
     *
     * @param string|int $userRoleId
     * @param \Illuminate\Support\Collection $rolePermissionMenus A collection of role permission menus.
     * @return array A list of menu_id that can be shown.
     */
    public function filterListMenuIdCanBeShowing($userRoleId, $rolePermissionMenus) {
        if (empty($rolePermissionMenus)) {
            return [];
        }

        $filteredRolePermissionMenus = $rolePermissionMenus;
        if ($userRoleId !== null) {
            $filteredRolePermissionMenus = $rolePermissionMenus->filter(function ($rolePermissionMenu) {
                return in_array($rolePermissionMenu->permission_type, [
                    ValueUtil::constToValue('role_permission.permission_type.REGISTER'),
                    ValueUtil::constToValue('role_permission.permission_type.READ_ONLY'),
                ]);
            });
        }

        return $filteredRolePermissionMenus->pluck('menu_id')->all();
    }

    /**
     * Checks if the required permission type is present in the request attributes.
     *
     * @param string $requiredPermissionTypeConst The required permission type constant.
     * @return bool True if the required permission type is present, false otherwise.
     */
    public static function checkPermission($requiredPermissionTypeConst) {
        if (
            ! isset($requiredPermissionTypeConst)
            || ! $permissionTypeValue = ValueUtil::constToValue("role_permission.permission_type.{$requiredPermissionTypeConst}")
        ) {
            throw new UnexpectedValueException('Invalid permission type constant.');
        }

        // Get permission type from request (already been set in AuthorizeAccess middleware)
        $permissionType = request()->attributes->get(self::REQUEST_ATTRIBUTE_PERMISSION_TYPE);
        if (! isset($permissionType)) {
            throw new UnexpectedValueException('Permission type is not set. Make sure to call "authorize.access" middleware first.');
        }

        return $permissionType === $permissionTypeValue;
    }

    /**
     * Get list menu id can be showing
     *
     * @param string|int $userRoleId
     * @return array
     */
    public function getListMenuIdCanBeShowing($userRoleId) {
        $rolePermissionMenus = $this->getRolePermissionMenus($userRoleId);
        $listMenuIdCanBeShowing = $this->filterListMenuIdCanBeShowing($userRoleId, $rolePermissionMenus);

        return $listMenuIdCanBeShowing;
    }
}
