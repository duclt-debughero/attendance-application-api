<?php

namespace App\Repositories;

use App\Models\RolePermission;
use Exception;
use Illuminate\Support\Facades\{DB, Log};

class RolePermissionRepository extends BaseRepository
{
    public function getModel() {
        return RolePermission::class;
    }

    /**
     * Get role permission By user role id and menu id
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

    /**
     * Update role permission
     *
     * @param string|int $userRoleId
     * @param string|int $menuId
     * @param array $params
     * @return mixed
     */
    public function updateRolePermission($userRoleId, $menuId, $params) {
        DB::beginTransaction();
        try {
            $rolePermission = RolePermission::query()
                ->where('role_permission.user_role_id', $userRoleId)
                ->where('role_permission.menu_id', $menuId)
                ->whereValidDelFlg()
                ->first();

            if (! $rolePermission->update($params)) {
                DB::rollBack();
                return false;
            }

            DB::commit();
            return $rolePermission;
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();

            return false;
        }
    }
}
