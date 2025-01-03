<?php

namespace App\Http\Controllers\Api;

use App\Enums\ApiCodeNo;
use App\Libs\ApiBusUtil;
use App\Repositories\UserRoleRepository;
use App\Requests\Api\UserRole\{
    AddRequest,
    EditRequest,
};
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
            $userRoles = $this->userRoleRepository->search($params)->get();
            if ($userRoles->isEmpty()) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::RECORD_NOT_EXISTS);
            }

            // Convert data for user role list
            $userRoles = $this->userRoleService->convertDataUserRole($userRoles, false);
            $userRoles = $this->pagination($userRoles)->toArray();

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
        try {
            // Get user role by user role id
            $userRole = $this->userRoleRepository->getUserRoleByUserRoleId($userRoleId);
            if ($userRole->isEmpty()) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::URL_NOT_EXISTS);
            }

            // Convert data for user role detail
            $userRole = $this->userRoleService->convertDataUserRole($userRole);

            return ApiBusUtil::successResponse($userRole);
        } catch (Exception $e) {
            Log::error($e);

            return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
        }
    }

    /**
     * Role Create
     * POST /api/v1/role/create
     *
     * @param AddRequest $request
     */
    public function create(AddRequest $request) {
        try {
            $params = $request->only(['user_role_name', 'role_permissions']);

            // Create user role and role permission
            $userRole = $this->userRoleService->handleCreateUserRole($params);
            if ($userRole->isEmpty()) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
            }

            // Convert data for user role detail
            $userRole = $this->userRoleService->convertDataUserRole($userRole);

            return ApiBusUtil::successResponse($userRole);
        } catch (Exception $e) {
            Log::error($e);

            return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
        }
    }

    /**
     * Role Update
     * POST /api/v1/role/update
     *
     * @param EditRequest $request
     * @param string|int $userRoleId
     */
    public function update(EditRequest $request, $userRoleId) {
        try {
            $params = $request->only(['user_role_name', 'role_permissions']);

            // Get user role by user role id
            $userRole = $this->userRoleRepository->getUserRoleByUserRoleId($userRoleId);
            if ($userRole->isEmpty()) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
            }

            // Update user role and role permission
            $userRole = $this->userRoleService->handleUpdateUserRole($userRoleId, $params);
            if ($userRole->isEmpty()) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
            }

            // Convert data for user role detail
            $userRole = $this->userRoleService->convertDataUserRole($userRole);

            return ApiBusUtil::successResponse($userRole);
        } catch (Exception $e) {
            Log::error($e);

            return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
        }
    }

    /**
     * Role Delete
     * POST /api/v1/role/delete
     *
     * @param Request $request
     * @param string|int $userRoleId
     */
    public function delete(Request $request, $userRoleId) {
        try {
            // Get user role by user role id
            $userRole = $this->userRoleRepository->getUserRoleByUserRoleId($userRoleId);
            if ($userRole->isEmpty()) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
            }

            // Delete user role and role permission
            $userRole = $this->userRoleService->handleDeleteUserRole($userRoleId);
            if ($userRole === false) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
            }

            return ApiBusUtil::successResponse();
        } catch (Exception $e) {
            Log::error($e);

            return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
        }
    }
}
