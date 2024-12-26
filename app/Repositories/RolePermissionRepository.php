<?php

namespace App\Repositories;

use App\Models\RolePermission;
use Exception;
use Illuminate\Support\Facades\Log;

class RolePermissionRepository extends BaseRepository
{
    public function getModel() {
        return RolePermission::class;
    }

    /**
     * Get role_permission By user_role_id and menu_id
     *
     * @param int $userRoleId
     * @param int $menuId
     * @return mixed
     */
    public function getRolePermissionByUserRoleIdAndMenuId($userRoleId, $menuId) {
        try {
            $rolePermission = RolePermission::query()
                ->join('role_menu', function ($join) {
                    $join
                        ->on('role_menu.menu_id', '=', 'role_permission.menu_id')
                        ->whereValidDelFlg();
                })
                ->join('user_role', function ($join) {
                    $join
                        ->on('user_role.user_role_id', '=', 'role_permission.user_role_id')
                        ->whereValidDelFlg();
                })
                ->where('role_permission.user_role_id', $userRoleId)
                ->where('role_permission.menu_id', $menuId)
                ->whereValidDelFlg()
                ->first();

            return $rolePermission;
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }
}
