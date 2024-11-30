<?php

use App\Enums\ApiCodeNo;
use App\Libs\ApiBusUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::fallback(function (Request $request) {
    return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::URL_NOT_EXISTS);
});
