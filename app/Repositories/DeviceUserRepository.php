<?php

namespace App\Repositories;

use App\Libs\ValueUtil;
use App\Models\DeviceUser;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DeviceUserRepository extends BaseRepository
{
    public function getModel() {
        return DeviceUser::class;
    }

    /**
     * Create or update login token for API Login
     *
     * @param int $userId
     * @param string $deviceToken
     * @return mixed
     */
    public function createUpdateLoginToken($userId, $deviceToken) {
        try {
            $accessToken = uniqid(base64_encode(Str::random(60)));
            $accessTokenExpire = Carbon::now()->addDays(ValueUtil::get('common.access_token_expire'));
            $refreshToken = uniqid(base64_encode(Str::random(60)));
            $refreshTokenExpire = Carbon::now()->addDays(ValueUtil::get('common.refresh_token_expire'));

            $device = $this->getUserDevice($userId, $deviceToken);
            if (empty($device)) {
                return $this->create([
                    'user_id' => $userId,
                    'device_token' => $deviceToken,
                    'access_token' => $accessToken,
                    'access_token_expire' => $accessTokenExpire,
                    'refresh_token' => $refreshToken,
                    'refresh_token_expire' => $refreshTokenExpire,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            }

            return $this->update($device->device_id, [
                'access_token' => $accessToken,
                'access_token_expire' => $accessTokenExpire,
                'refresh_token' => $refreshToken,
                'refresh_token_expire' => $refreshTokenExpire,
                'updated_by' => $userId,
            ]);
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }

    /**
     * Update logout token for API Logout
     *
     * @param string|int $deviceId
     * @return mixed
     */
    public function updateLogoutToken($deviceId) {
        try {
            return $this->update($deviceId, [
                'access_token' => null,
                'access_token_expire' => null,
                'refresh_token' => null,
                'refresh_token_expire' => null,
            ]);
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }

    /**
     * Get device by user id, device token
     *
     * @param string|int $userId
     * @param string $deviceToken
     * @return mixed
     */
    public function getUserDevice($userId, $deviceToken) {
        try {
            $device = DeviceUser::query()
                ->where('device_user.user_id', $userId)
                ->where('device_user.device_token', $deviceToken)
                ->whereValidDelFlg()
                ->first();

            return $device;
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }

    /**
     * Update token for API Refresh
     *
     * @param string|int $deviceId
     * @param string|int $userId
     * @return mixed
     */
    public function refreshToken($deviceId, $userId) {
        try {
            return $this->update($deviceId, [
                'access_token' => uniqid(base64_encode(Str::random(60))),
                'access_token_expire' => Carbon::now()->addDays(ValueUtil::get('common.access_token_expire')),
                'refresh_token' => uniqid(base64_encode(Str::random(60))),
                'refresh_token_expire' => Carbon::now()->addDays(ValueUtil::get('common.refresh_token_expire')),
                'updated_by' => $userId,
            ]);
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }
}
