<?php
namespace App\Enums;

enum ApiStatusCode: int {
    case OK = 200;
    case NG400 = 400;
    case NG401 = 401;
    case NG403 = 403;
    case NG404 = 404;
    case NG500 = 500;
    case NG503 = 503;
}
