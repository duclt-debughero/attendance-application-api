<?php

namespace App\Http\Controllers\Api;

use App\Enums\ApiCodeNo;
use App\Libs\{
    ApiBusUtil,
    DateUtil,
    EncryptUtil,
};
use App\Repositories\{
    MstUserRepository,
    DeviceUserRepository,
};
use App\Requests\Api\Auth\{
    LoginRequest,
    LogoutRequest,
    RefreshRequest,
};
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends ApiBaseController
{
    public function __construct(
        private MstUserRepository $mstUserRepository,
        private DeviceUserRepository $deviceUserRepository,
    ) {
    }

    /**
     * Login
     * POST /api/v1/login
     *
     * @param LoginRequest $request
     */
    public function login(LoginRequest $request) {
        try {
            $emailAddress = $request->email_address;
            $password = $request->password;
            $deviceToken = $request->device_token;

            // Get user by email address and check password
            $user = $this->mstUserRepository->getUserByEmailAddress($emailAddress);
            if (empty($user) || $user->password !== EncryptUtil::encryptSha256($password)) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::LOGIN_FAILED);
            }

            // Create access_token, refresh_token
            $device = $this->deviceUserRepository->createUpdateLoginToken($user->user_id, $deviceToken);
            if (empty($device)) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
            }

            // Update last login time
            $updateLastLoginTime = $this->mstUserRepository->updateLastLoginTime($user->user_id);
            if (empty($updateLastLoginTime)) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
            }

            return ApiBusUtil::successResponse(
                [
                    'user_id' => $user->user_id,
                    'device_id' => $device->device_id,
                    'access_token' => $device->access_token,
                    'refresh_token' => $device->refresh_token,
                ],
            );
        } catch (Exception $e) {
            Log::error($e);

            return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
        }
    }

    /**
     * Logout
     * POST /api/v1/logout
     *
     * @param LogoutRequest $request
     */
    public function logout(LogoutRequest $request) {
        try {
            $deviceId = $request->device_id;
            $accessToken = $request->header('Authorization') ?? $request->header('authorization');

            // Check device
            $device = $this->deviceUserRepository->findById($deviceId);
            if (empty($device) || $device->access_token !== $accessToken) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
            }

            // Update logout token
            $updateResult = $this->deviceUserRepository->updateLogoutToken($deviceId);
            if (empty($updateResult)) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
            }

            return ApiBusUtil::successResponse();
        } catch (Exception $e) {
            Log::error($e);

            return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
        }
    }

    /**
     * Refresh Access Token
     * POST /api/v1/refresh
     *
     * @param RefreshRequest $request
     */
    public function refresh(RefreshRequest $request) {
        try {
            $now = Carbon::now();
            $deviceId = $request->device_id;
            $accessToken = $request->access_token;
            $refreshToken = $request->refresh_token;

            // Get device and check DB connection fails or access_token is invalid
            $device = $this->deviceUserRepository->findById($deviceId);
            if (empty($device) || $device->access_token != $accessToken) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::ISSUE_ACCESS_TOKEN_FAILED);
            }

            $dataRes = [
                'user_id' => $device->user_id ?? null,
                'device_id' => $device->device_id ?? null,
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
            ];

            // Access_token_expire has not expired yet
            if (Carbon::parse($device->access_token_expire)->greaterThanOrEqualTo($now)) {
                return ApiBusUtil::successResponse($dataRes);
            }

            // Check refresh_token
            if ($device->refresh_token != $refreshToken || Carbon::parse($device->refresh_token_expire)->lessThan($now)) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::ISSUE_ACCESS_TOKEN_FAILED);
            }

            // Refresh access_token, refresh_token and check DB connection fails
            $device = $this->deviceUserRepository->refreshToken($deviceId, $device->user_id);
            if (empty($device)) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
            }

            $dataRes['access_token'] = $device->access_token;
            $dataRes['refresh_token'] = $device->refresh_token;

            return ApiBusUtil::successResponse($dataRes);
        } catch (Exception $e) {
            Log::error($e);

            return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
        }
    }

    /**
     * Get Token Expiration By Access Token
     * GET /api/v1/token/expiration/{accessToken}
     *
     * @param Request $request
     * @param string $accessToken
     */
    public function getTokenExpirationByAccessToken(Request $request, $accessToken) {
        try {
            $device = $this->deviceUserRepository->getTokenExpirationByAccessToken($accessToken);
            if (empty($device)) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::RECORD_NOT_EXISTS);
            }

            return ApiBusUtil::successResponse([
                'access_token_expire' => DateUtil::formatDefaultDateTime($device->access_token_expire),
            ]);
        } catch (Exception $e) {
            Log::error($e);

            return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
        }
    }
}
