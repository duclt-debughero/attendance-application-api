<?php

namespace App\Http\Middleware;

use App\Enums\ApiCodeNo;
use App\Libs\ApiBusUtil;
use App\Libs\ValueUtil;
use App\Services\AuthorizeService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthorizeAccess
{
    public function __construct(
        private AuthorizeService $authorizeService,
    ) {
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     * @param int $menuId
     */
    public function handle(Request $request, Closure $next, int $menuId): Response {
        $userRoleId = Auth::user()->user_role_id ?? null;

        $request->attributes->set(AuthorizeService::REQUEST_ATTRIBUTE_PERMISSION_TYPE, ValueUtil::constToValue('role_permission.permission_type.REGISTER'));

        if (! isset($userRoleId)) {
            return $next($request);
        }

        $permissionType = $this->authorizeService->getCurrentUserPermissionType($userRoleId, $menuId);
        if ($permissionType == ValueUtil::constToValue('role_permission.permission_type.NO_ACCESS')) {
            return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::URL_NOT_EXISTS);
        }

        $request->attributes->set(AuthorizeService::REQUEST_ATTRIBUTE_PERMISSION_TYPE, $permissionType);

        return $next($request);
    }
}
