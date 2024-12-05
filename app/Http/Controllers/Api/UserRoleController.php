<?php

namespace App\Http\Controllers\Api;

use App\Enums\ApiCodeNo;
use App\Libs\ApiBusUtil;
use App\Repositories\UserRoleRepository;
use App\Services\UserRoleService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserRoleController extends ApiBaseController
{
    public function __construct(
        private UserRoleRepository $userRoleRepository,
        private UserRoleService $userRoleService,
    ) {
    }

    /**
     * Role List
     * GET /api/v1/role/list
     *
     * @param Request $request
     */
    public function list(Request $request) {
        try {
            $params = $request->only(['user_role_name']);

            // Get user role list by params search
            $userRoles = $this->userRoleRepository->search($params);
            if (empty($userRoles)) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::RECORD_NOT_EXISTS);
            }

            // Convert data for user role list
            $userRoles = $this->userRoleService->convertDataUserRole($userRoles);

            return ApiBusUtil::successResponse($userRoles);
        } catch (Exception $e) {
            Log::error($e);

            return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
        }
    }

    /**
     * Role Detail
     * GET /api/v1/role/detail
     *
     * @param Request $request
     * @param string|int $userRoleId
     */
    public function detail(Request $request, $userRoleId) {
        return ApiBusUtil::successResponse();
    }

    /**
     * Role Create
     * POST /api/v1/role/create
     *
     * @param Request $request
     */
    public function create(Request $request) {
        return ApiBusUtil::successResponse();
    }

    /**
     * Role Update
     * POST /api/v1/role/update
     *
     * @param Request $request
     */
    public function update(Request $request, $userRoleId) {
        return ApiBusUtil::successResponse();
    }

    /**
     * Role Delete
     * POST /api/v1/role/delete
     *
     * @param Request $request
     */
    public function delete(Request $request, $userRoleId) {
        return ApiBusUtil::successResponse();
    }
}
