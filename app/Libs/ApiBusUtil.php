<?php

namespace App\Libs;

use App\Enums\{
    ApiCodeNo,
    ApiStatusCode,
};

class ApiBusUtil
{
    /**
     * API success response
     *
     * @param array $data
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public static function successResponse($data = [], $headers = []) {
        return response()->json($data, 200, $headers);
    }

    /**
     * API error response
     *
     * @param ApiCodeNo|string|null $codeNo
     * @param ApiStatusCode|int $status
     * @param array|string|null $message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function errorResponse($codeNo = null, $status = 400, $message = null) {
        $status = $status instanceof ApiStatusCode ? $status->value : intval($status);

        return response()->json([
            'code' => $codeNo,
            'status' => $status,
            'message' => $message,
        ], $status);
    }

    /**
     * Pre-build error response
     *
     * @param ApiCodeNo $codeNo
     * @return \Illuminate\Http\JsonResponse
     */
    public static function preBuiltErrorResponse(ApiCodeNo $codeNo) {
        switch ($codeNo) {
            case ApiCodeNo::VALIDATE_PARAMETER:
                // API error No.001
                return self::errorResponse(
                    ApiCodeNo::VALIDATE_PARAMETER,
                    ApiStatusCode::NG400,
                    ConfigUtil::getMessage('API_VALIDATE_PARAMETER'),
                );
            case ApiCodeNo::REQUIRED_PARAMETER:
                // API error No.002
                return self::errorResponse(
                    ApiCodeNo::REQUIRED_PARAMETER,
                    ApiStatusCode::NG400,
                    ConfigUtil::getMessage('API_REQUIRED_PARAMETER'),
                );
            case ApiCodeNo::RECORD_NOT_EXISTS:
                // API error No.003
                return self::errorResponse(
                    ApiCodeNo::RECORD_NOT_EXISTS,
                    ApiStatusCode::NG400,
                    ConfigUtil::getMessage('API_RECORD_NOT_EXISTS'),
                );
            case ApiCodeNo::ACCESS_TOKEN_EXPIRED:
                // API error No.004
                return self::errorResponse(
                    ApiCodeNo::ACCESS_TOKEN_EXPIRED,
                    ApiStatusCode::NG400,
                    ConfigUtil::getMessage('API_ACCESS_TOKEN_EXPIRED'),
                );
            case ApiCodeNo::ISSUE_ACCESS_TOKEN_FAILED:
                // API error No.005
                return self::errorResponse(
                    ApiCodeNo::ISSUE_ACCESS_TOKEN_FAILED,
                    ApiStatusCode::NG400,
                    ConfigUtil::getMessage('API_ISSUE_ACCESS_TOKEN_FAILED'),
                );
            case ApiCodeNo::LOGIN_FAILED:
                // API error No.006
                return self::errorResponse(
                    ApiCodeNo::LOGIN_FAILED,
                    ApiStatusCode::NG401,
                    ConfigUtil::getMessage('API_LOGIN_FAILED'),
                );
            case ApiCodeNo::NOT_LOGIN:
                // API error No.007
                return self::errorResponse(
                    ApiCodeNo::NOT_LOGIN,
                    ApiStatusCode::NG403,
                    ConfigUtil::getMessage('API_NOT_LOGIN'),
                );
            case ApiCodeNo::URL_NOT_EXISTS:
                // API error No.008
                return self::errorResponse(
                    ApiCodeNo::URL_NOT_EXISTS,
                    ApiStatusCode::NG404,
                    ConfigUtil::getMessage('API_URL_NOT_EXISTS'),
                );
            case ApiCodeNo::DISABLED_USER_ERROR:
                // API error No.009
                return self::errorResponse(
                    ApiCodeNo::DISABLED_USER_ERROR,
                    ApiStatusCode::NG401,
                    ConfigUtil::getMessage('API_DISABLED_USER_ERROR'),
                );
            case ApiCodeNo::MAINTENANCE_MODE:
                // API error No.010
                return self::errorResponse(
                    ApiCodeNo::MAINTENANCE_MODE,
                    ApiStatusCode::NG503,
                    ConfigUtil::getMessage('API_MAINTENANCE_MODE'),
                );
            default:
                // API error No.999
                return self::errorResponse(
                    ApiCodeNo::SERVER_ERROR,
                    ApiStatusCode::NG500,
                    ConfigUtil::getMessage('API_SERVER_ERROR'),
                );
        }
    }
}
