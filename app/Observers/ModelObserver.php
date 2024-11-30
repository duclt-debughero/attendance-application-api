<?php

namespace App\Observers;

use App\Libs\ValueUtil;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ModelObserver
{
    public function creating(Model $model) {
        $notLoggedIn = ValueUtil::get('common.updated_by_not_logged_in');
        $model->created_at = Carbon::now();
        $model->updated_at = Carbon::now();
        $model->created_by = $model->isDirty('created_by') ? $model->created_by : Auth::user()->user_id ?? $notLoggedIn;
        $model->updated_by = $model->isDirty('updated_by') ? $model->updated_by : Auth::user()->user_id ?? $notLoggedIn;
        $model->deleted_by = null;
        $model->deleted_at = null;
    }

    public function updating(Model $model) {
        $notLoggedIn = ValueUtil::get('common.updated_by_not_logged_in');
        if ($model->del_flg == ValueUtil::constToValue('common.del_flg.INVALID')) {
            $model->deleted_at = Carbon::now();
            $model->deleted_by = $model->isDirty('deleted_by') ? $model->deleted_by : Auth::user()->user_id ?? $notLoggedIn;
        } else {
            $model->updated_at = Carbon::now();
            $model->updated_by = $model->isDirty('updated_by') ? $model->updated_by : Auth::user()->user_id ?? $notLoggedIn;
        }
    }

    public function saving(Model $model) {
        Log::info($model->toArray());
    }
}
