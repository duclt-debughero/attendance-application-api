<?php

namespace App\Http\Controllers\Api;

use App\Enums\ApiCodeNo;
use App\Libs\ApiBusUtil;
use App\Repositories\MstUserRepository;
use App\Services\MstUserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends ApiBaseController
{
    public function __construct(
        private MstUserRepository $mstUserRepository,
        private MstUserService $mstUserService,
    ) {
    }

    /**
     * User List
     * GET /api/v1/user/list
     *
     * @param Request $request
     */
    public function list(Request $request) {
        try {
            $params = $request->only([
                'email_address',
                'user_name',
                'telephone_number',
                'user_role_name',
            ]);

            // Get user list by params search
            $users = $this->mstUserRepository->search($params);
            if (empty($users)) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::RECORD_NOT_EXISTS);
            }

            // Convert data for user list
            $users = $this->mstUserService->convertDataUserList($users);

            return ApiBusUtil::successResponse($users);
        } catch (Exception $e) {
            Log::error($e);

            return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
        }
    }

    /**
     * User Detail
     * GET /api/v1/user/detail
     *
     * @param Request $request
     * @param string|int $userId
     */
    public function detail(Request $request, $userId) {
        return ApiBusUtil::successResponse();
    }

    /**
     * User Create
     * POST /api/v1/user/create
     *
     * @param Request $request
     */
    public function create(Request $request) {
        return ApiBusUtil::successResponse();
    }

    /**
     * User Update
     * POST /api/v1/user/update
     *
     * @param Request $request
     */
    public function update(Request $request, $userId) {
        return ApiBusUtil::successResponse();
    }

    /**
     * User Delete
     * POST /api/v1/user/delete
     *
     * @param Request $request
     */
    public function delete(Request $request, $userId) {
        return ApiBusUtil::successResponse();
    }
}
