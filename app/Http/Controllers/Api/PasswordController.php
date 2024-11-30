<?php

namespace App\Http\Controllers\Api;

use App\Enums\ApiCodeNo;
use App\Libs\ApiBusUtil;
use App\Repositories\MstUserRepository;
use App\Requests\Api\Password\{
    ForgotRequest,
    ResetRequest,
};
use App\Services\PasswordService;
use Exception;
use Illuminate\Support\Facades\Log;

class PasswordController extends ApiBaseController
{
    public function __construct(
        private MstUserRepository $mstUserRepository,
        private PasswordService $passwordService,
    ) {
    }

    /**
     * Forgot Password
     * POST /api/v1/password/forgot
     *
     * @param ForgotRequest $request
     */
    public function forgot(ForgotRequest $request) {
        try {
            $emailAddress = $request->email_address;

            // Get user by email address
            $user = $this->mstUserRepository->getUserByEmailAddress($emailAddress);
            if (empty($user)) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::RECORD_NOT_EXISTS);
            }

            // Update password token and send email
            $user = $this->passwordService->updatePasswordToken($user);
            if (empty($user)) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
            }

            return ApiBusUtil::successResponse();
        } catch (Exception $e) {
            Log::error($e);

            return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
        }
    }

    /**
     * Reset Password
     * POST /api/v1/password/reset
     *
     * @param ResetRequest $request
     */
    public function reset(ResetRequest $request) {
        try {
            $password = $request->password;
            $passwordToken = $request->password_token;

            // Get user by password token
            $user = $this->mstUserRepository->getUserByPasswordToken($passwordToken);
            if (empty($user)) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::RECORD_NOT_EXISTS);
            }

            // Check password equals email address
            if ($password == $user->email_address) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::VALIDATE_PARAMETER);
            }

            // Update the user's password and clear the password reset token and expiry
            $updatedUser = $this->mstUserRepository->update($user->user_id, [
                'password' => $password,
                'password_token' => null,
                'password_token_expire' => null,
                'updated_by' => $user->user_id,
            ]);

            if (empty($updatedUser)) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
            }

            return ApiBusUtil::successResponse();
        } catch (Exception $e) {
            Log::error($e);

            return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
        }
    }
}
