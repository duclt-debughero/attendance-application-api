<?php

namespace App\Repositories;

use App\Models\UserRole;
use Exception;
use Illuminate\Support\Facades\Log;

class UserRoleRepository extends BaseRepository
{
    public function getModel() {
        return UserRole::class;
    }

    /**
     * Get query user role
     *
     * @param array $columns
     * @return mixed
     */
    public function getQueryUserRole($columns = []) {
        try {
            $defaultColumns = [
                'user_role.user_role_id',
                'user_role.user_role_name',
                'mst_user.user_name as last_updated_by',
                'user_role.updated_at as last_updated_at',
                'role_menu.menu_id',
                'role_menu.menu_name',
                'role_permission.permission_id',
                'role_permission.permission_type',
            ];

            $query = UserRole::query()
                ->select(array_merge($defaultColumns, $columns))
                ->leftJoin('mst_user', function ($query) {
                    $query
                        ->on('mst_user.user_id', '=', 'user_role.updated_by')
                        ->whereValidDelFlg();
                })
                ->leftJoin('role_permission', function ($query) {
                    $query
                        ->on('role_permission.user_role_id', '=', 'user_role.user_role_id')
                        ->whereValidDelFlg();
                })
                ->leftJoin('role_menu', function ($query) {
                    $query
                        ->on('role_menu.menu_id', '=', 'role_permission.menu_id')
                        ->whereValidDelFlg();
                })
                ->whereValidDelFlg();

            return $query;
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }

    /**
     * Search for user role
     *
     * @param array $params
     * @return mixed
     */
    public function search($params = []) {
        try {
            $query = $this->getQueryUserRole();

            // Search for user role name
            if (isset($params['user_role_name'])) {
                $query->where('user_role.user_role_name', 'like', "%{$params['user_role_name']}%");
            }

            return $query;
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }

    /**
     * Get user role by user role id
     *
     * @param string|int $userRoleId
     * @return mixed
     */
    public function getUserRoleByUserRoleId($userRoleId) {
        try {
            $query = $this->getQueryUserRole()->where('user_role.user_role_id', $userRoleId);

            return $query->get();
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }
}
