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
     * Search for user_role
     *
     * @param array $params
     * @return mixed
     */
    public function search($params = []) {
        try {
            $query = UserRole::query()
                ->select([
                    'user_role.user_role_id',
                    'user_role.user_role_name',
                    'mst_user.user_name as last_updated_by',
                    'user_role.updated_at as last_updated_at',
                ])
                ->leftJoin('mst_user', function ($query) {
                    $query
                        ->on('mst_user.user_id', '=', 'user_role.updated_by')
                        ->whereValidDelFlg();
                })
                ->whereValidDelFlg();

            // Search for user role name
            if (isset($params['user_role_name'])) {
                $query->where('user_role.user_role_name', 'like', "%{$params['user_role_name']}%");
            }

            return $query->get();
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }
}
