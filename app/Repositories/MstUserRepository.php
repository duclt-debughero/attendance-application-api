<?php

namespace App\Repositories;

use App\Libs\{
    EncryptUtil,
    ValueUtil,
};
use App\Models\MstUser;
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
}
