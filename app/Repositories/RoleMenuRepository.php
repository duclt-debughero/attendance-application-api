<?php

namespace App\Repositories;

use App\Models\RoleMenu;
use Illuminate\Support\Facades\Log;
use App\Repositories\BaseRepository;
use Exception;

class RoleMenuRepository extends BaseRepository
{
    public function getModel() {
        return RoleMenu::class;
    }

    /**
     * Retrieves a list of role permission menus for a given user_role_id.
     *
     * @param int $userRoleId The ID of the user role.
     * @return mixed A collection of role permission menus or false on failure.
     */
    public function getRolePermissionMenus($userRoleId) {
        try {
            $query = RoleMenu::query()
                ->select([
                    'role_menu.menu_id',
                    'role_permission.permission_type',
                ])
                ->leftJoin('role_permission', function ($join) use ($userRoleId) {
                    $join
                        ->on('role_permission.menu_id', '=', 'role_menu.menu_id')
                        ->where('role_permission.user_role_id', $userRoleId)
                        ->whereValidDelFlg();
                })
                ->whereValidDelFlg()
                ->get();

            return $query;
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }
}
