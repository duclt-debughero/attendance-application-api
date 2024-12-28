<?php

namespace App\Http\Controllers\Api;

use App\Enums\ApiCodeNo;
use App\Libs\ApiBusUtil;
use App\Repositories\MstUserRepository;
use App\Requests\Api\User\{
    AddRequest,
    EditRequest,
};
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
            if ($users->isEmpty()) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::RECORD_NOT_EXISTS);
            }

            // Convert data for user list
            $users = $this->mstUserService->convertDataUserList($users);
            $users = $this->pagination($users)->toArray();

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
        try {
            // Get user by user id
            $user = $this->mstUserRepository->getUserByUserId($userId);
            if (empty($user)) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::URL_NOT_EXISTS);
            }

            // Convert data for user detail
            $user = $this->mstUserService->convertDataUserDetail($user);

            return ApiBusUtil::successResponse($user);
        } catch (Exception $e) {
            Log::error($e);

            return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
        }
    }

    /**
     * User Create
     * POST /api/v1/user/create
     *
     * @param AddRequest $request
     */
    public function create(AddRequest $request) {
        try {
            $params = $request->only([
                'email_address',
                'password',
                'user_name',
                'telephone_number',
                'user_role_id',
            ]);

            // Create user
            $user = $this->mstUserRepository->create($params);
            if (empty($user)) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
            }

            // Convert data for user detail
            $user = $this->mstUserRepository->getUserByUserId($user->user_id);
            $user = $this->mstUserService->convertDataUserDetail($user);

            return ApiBusUtil::successResponse($user);
        } catch (Exception $e) {
            Log::error($e);

            return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
        }
    }

    /**
     * User Update
     * POST /api/v1/user/update
     *
     * @param EditRequest $request
     * @param string|int $userId
     */
    public function update(EditRequest $request, $userId) {
        try {
            $params = $request->only([
                'password',
                'user_name',
                'telephone_number',
                'user_role_id',
            ]);

            // Unset password if empty
            if (empty($params['password'])) {
                unset($params['password']);
            }

            // Get user by user id
            $user = $this->mstUserRepository->getUserByUserId($userId);
            if (empty($user)) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
            }

            // Update user
            $user = $this->mstUserRepository->update($userId, $params);
            if (empty($user)) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
            }

            // Convert data for user detail
            $user = $this->mstUserRepository->getUserByUserId($user->user_id);
            $user = $this->mstUserService->convertDataUserDetail($user);

            return ApiBusUtil::successResponse($user);
        } catch (Exception $e) {
            Log::error($e);

            return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
        }
    }

    /**
     * User Delete
     * POST /api/v1/user/delete
     *
     * @param Request $request
     * @param string|int $userId
     */
    public function delete(Request $request, $userId) {
        try {
            // Get user by user id
            $user = $this->mstUserRepository->getUserByUserId($userId);
            if (empty($user)) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
            }

            // Delete user
            $user = $this->mstUserRepository->deleteById($userId);
            if (empty($user)) {
                return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
            }

            return ApiBusUtil::successResponse();
        } catch (Exception $e) {
            Log::error($e);

            return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::SERVER_ERROR);
        }
    }
}
