<?php

namespace App\Http\Middleware;

use App\Enums\ApiCodeNo;
use App\Libs\ApiBusUtil;
use App\Services\AuthorizeService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthorizeCheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     * @param string $requiredPermissionTypeConst
     */
    public function handle(Request $request, Closure $next, string $requiredPermissionTypeConst): Response {
        if (! AuthorizeService::checkPermission($requiredPermissionTypeConst)) {
            return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::URL_NOT_EXISTS);
        }

        return $next($request);
    }
}
