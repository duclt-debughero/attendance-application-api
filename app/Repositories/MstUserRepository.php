<?php

namespace App\Repositories;

use App\Libs\EncryptUtil;
use App\Models\MstUser;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\{DB, Log};

class MstUserRepository extends BaseRepository
{
    public function getModel() {
        return MstUser::class;
    }

    /**
     * Get query mst user
     *
     * @param array $columns
     * @return mixed
     */
    public function getQueryMstUser($columns = []) {
        $defaultColumns = [
            'mst_user.user_id',
            'mst_user.email_address',
            'mst_user.user_name',
            'mst_user.telephone_number',
            'mst_user.last_login_time',
            'user_role.user_role_id',
            'user_role.user_role_name',
        ];

        $query = MstUser::query()
            ->select(array_merge($defaultColumns, $columns))
            ->leftJoin('user_role', function ($join) {
                $join
                    ->on('mst_user.user_role_id', '=', 'user_role.user_role_id')
                    ->whereValidDelFlg();
            })
            ->whereValidDelFlg();

        return $query;
    }

    /**
     * Search for mst user
     *
     * @param array $params
     * @return mixed
     */
    public function search($params = []) {
        try {
            $query = $this->getQueryMstUser();

            // Search for email address
            if (isset($params['email_address'])) {
                $query->where('mst_user.email_address', EncryptUtil::encryptAes256($params['email_address']));
            }

            // Search for user name
            if (isset($params['user_name'])) {
                $query->where(DB::Raw($this->dbDecryptAes256('mst_user.user_name')), 'like', "%{$params['user_name']}%");
            }

            // Search for telephone number
            if (isset($params['telephone_number'])) {
                $query->where('mst_user.telephone_number', $params['telephone_number']);
            }

            // Search for user role
            if (isset($params['user_role_name'])) {
                $query->where('user_role.user_role_name', $params['user_role_name']);
            }

            return $query;
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }

    /**
     * Get user by user id
     *
     * @param string|int $userId
     * @return mixed
     */
    public function getUserByUserId($userId) {
        try {
            $query = $this->getQueryMstUser()->where('mst_user.user_id', $userId);

            return $query->first();
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }

    /**
     * Check email address unique
     *
     * @param string $emailAddress
     * @param string $excluded exclude a record by user id
     * @param mixed|null $excludedId
     * @return bool
     */
    public function isUniqueEmailAddress($emailAddress, $excludedId = null) {
        try {
            $query = MstUser::query()
                ->where('mst_user.email_address', EncryptUtil::encryptAes256($emailAddress))
                ->whereValidDelFlg();

            if (! empty($excludedId)) {
                $query->where('mst_user.user_id', '<>', $excludedId);
            }

            return ! $query->first();
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }

    /**
     * Get user by email address
     *
     * @param string $emailAddress
     * @return mixed
     */
    public function getUserByEmailAddress($emailAddress) {
        try {
            $query = MstUser::query()
                ->select([
                    'mst_user.user_id',
                    'mst_user.email_address',
                    'mst_user.password',
                    'mst_user.password_token',
                    'mst_user.password_token_expire',
                    'mst_user.user_name',
                    'mst_user.telephone_number',
                    'mst_user.last_login_time',
                    'user_role.user_role_id',
                    'user_role.user_role_name',
                ])
                ->leftJoin('user_role', function ($join) {
                    $join
                        ->on('mst_user.user_role_id', '=', 'user_role.user_role_id')
                        ->whereValidDelFlg();
                })
                ->where('mst_user.email_address', EncryptUtil::encryptAes256($emailAddress))
                ->whereValidDelFlg()
                ->first();

            return $query;
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }

    /**
     * Login web using access token
     *
     * @param string $accessToken
     * @return mixed
     */
    public function loginWithAccessToken($accessToken) {
        try {
            $query = MstUser::query()
                ->join('device_user', function ($join) use ($accessToken) {
                    $join
                        ->on('device_user.user_id', '=', 'mst_user.user_id')
                        ->where('device_user.access_token', $accessToken)
                        ->whereValidDelFlg();
                })
                ->whereValidDelFlg()
                ->first();

            return $query;
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }

    /**
     * Update last login time
     *
     * @param string|int $userId
     * @return mixed
     */
    public function updateLastLoginTime($userId) {
        try {
            return $this->update($userId, [
                'last_login_time' => Carbon::now(),
                'updated_by' => $userId,
            ]);
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }

    /**
     * Get user by password token
     *
     * @param mixed $passwordToken
     * @return mixed
     */
    public function getUserByPasswordToken($passwordToken) {
        try {
            $query = MstUser::query()
                ->where('password_token', $passwordToken)
                ->where('password_token_expire', '>=', Carbon::now())
                ->whereValidDelFlg()
                ->first();

            return $query;
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }
}
