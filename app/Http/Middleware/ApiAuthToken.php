<?php

namespace App\Http\Middleware;

use App\Enums\ApiCodeNo;
use App\Libs\ApiBusUtil;
use Carbon\Carbon;
use Closure;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiAuthToken extends Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     * @param array $guards
     */
    public function handle($request, Closure $next, ...$guards) {
        $now = Carbon::now();
        $loginUser = Auth::user();
        $guards = ['api'];

        $this->authenticate($request, $guards);

        // Check access_token_expire
        if (Carbon::parse($loginUser->access_token_expire)->lessThan($now)) {
            return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::ACCESS_TOKEN_EXPIRED);
        }

        return $next($request);
    }

    /**
     * Handle an unauthenticated user.
     *
     * @param Request $request
     * @param array $guards
     * @throws \Illuminate\Auth\AuthenticationException
     * @return void
     */
    protected function unauthenticated($request, array $guards) {
        throw new HttpResponseException(ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::NOT_LOGIN));
    }
}
