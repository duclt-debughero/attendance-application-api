<?php

namespace App\Http\Controllers\Api;

use App\Libs\ApiBusUtil;
use Illuminate\Http\Request;

class AuthController extends ApiBaseController
{
    /**
     * Login
     * POST /api/v1/login
     * 
     * @param Request $request
     */
    public function login(Request $request) {
        return ApiBusUtil::successResponse();
    }

    /**
     * Logout
     * POST /api/v1/logout
     * 
     * @param Request $request
     */
    public function logout(Request $request) {
        return ApiBusUtil::successResponse();
    }

    /**
     * Refresh Access Token
     * POST /api/v1/refresh
     * 
     * @param Request $request
     */
    public function refresh(Request $request) {
        return ApiBusUtil::successResponse();
    }
}
