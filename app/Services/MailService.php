<?php

namespace App\Services;

use App\Mail\M001;
use Exception;
use Illuminate\Support\Facades\{Log, Mail};

class MailService
{
    /**
     * Send M001 Mail
     *
     * @param $emailTo
     * @param $paramsMail
     */
    public function sendM001Mail($emailTo, $paramsMail) {
        try {
            Mail::to($emailTo)
                ->bcc(config('attendance-application.system_admin_mail'))
                ->send(new M001($paramsMail));

            return true;
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }
}
