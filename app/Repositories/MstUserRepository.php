<?php

namespace App\Repositories;

use App\Libs\{
    EncryptUtil,
    ValueUtil,
};
use App\Models\MstUser;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class MstUserRepository extends BaseRepository
{
    public function getModel() {
        return MstUser::class;
    }

    /**
     * Check email address unique
     *
     * @param string $emailAddress
     * @param string $excluded exclude a record by user_id
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
            $result = MstUser::query()
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

            return $result;
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }

    /**
     * Login web using access_token
     *
     * @param string $accessToken
     * @return mixed
     */
    public function loginWithAccessToken($accessToken) {
        try {
            $mstUser = MstUser::query()
                ->join('device_user', function ($join) use ($accessToken) {
                    $join
                        ->on('device_user.user_id', '=', 'mst_user.user_id')
                        ->where('device_user.access_token', $accessToken)
                        ->whereValidDelFlg();
                })
                ->whereValidDelFlg()
                ->first();

            return $mstUser;
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
            $mstUser = MstUser::query()
                ->where('password_token', $passwordToken)
                ->where('password_token_expire', '>=', Carbon::now())
                ->whereValidDelFlg()
                ->first();

            return $mstUser;
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }
}
