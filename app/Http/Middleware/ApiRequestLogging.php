<?php

namespace App\Http\Middleware;

use App\Libs\ValueUtil;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiRequestLogging
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response {
        $routeNameToApiName = ValueUtil::get('api.route_name_to_api_name');
        $routeName = $request->route()->getName() ?? null;
        $apiName = isset($routeName) && isset($routeNameToApiName[$routeName]) ? $routeNameToApiName[$routeName] : '-';
        $ipAddress = $request->ip() ?? '-';

        // [date time] [API name] [IP address]
        $message = "{$apiName} {$ipAddress}";

        Log::info($message);

        return $next($request);
    }
}
