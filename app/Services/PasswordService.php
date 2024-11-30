<?php

namespace App\Services;

use App\Libs\{
    DateUtil,
    ValueUtil,
};
use App\Models\MstUser;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\{DB, Log};
use Illuminate\Support\Str;

class PasswordService
{
    public function __construct(
        private MailService $mailService,
    ) {
    }

    /**
     * Update  password reset token.
     *
     * @param MstUser $user
     * @return bool
     */
    public function updatePasswordToken($user) {
        DB::beginTransaction();
        try {
            $user->password_token = Str::random(ValueUtil::get('common.number_rand_token_password'));
            $user->password_token_expire = Carbon::now()->addHours(ValueUtil::get('common.password_token_expire'));
            $user->updated_by = $user->user_id;

            if ($user->save()) {
                // Send email
                $checkSendEmail = $this->mailService->sendM001Mail($user->email_address, [
                    'passwordToken' => $user->password_token,
                    'passwordTokenExpire' => DateUtil::formatDateTime($user->password_token_expire, 'Y/m/d H:i'),
                ]);

                if (! $checkSendEmail) {
                    DB::rollBack();
                    return false;
                }
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();

            return false;
        }
    }
}
