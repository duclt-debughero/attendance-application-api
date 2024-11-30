<?php
namespace App\Enums;

enum ApiCodeNo: string {
    case VALIDATE_PARAMETER = '001';
    case REQUIRED_PARAMETER = '002';
    case RECORD_NOT_EXISTS = '003';
    case ACCESS_TOKEN_EXPIRED = '004';
    case ISSUE_ACCESS_TOKEN_FAILED = '005';
    case LOGIN_FAILED = '006';
    case NOT_LOGIN = '007';
    case URL_NOT_EXISTS = '008';
    case DISABLED_USER_ERROR = '009';
    case MAINTENANCE_MODE = '010';
    case SERVER_ERROR = '999';
}
