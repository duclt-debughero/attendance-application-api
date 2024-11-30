<?php

use App\Enums\ApiCodeNo;
use App\Http\Controllers\Api\{
    AuthController,
    PasswordController,
};
use App\Http\Middleware\{
    ApiAuthToken,
    ApiAuthorizeAccess,
    ApiAuthorizeCheckPermission,
};
use App\Libs\{
    ApiBusUtil,
    ValueUtil,
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('auth.refresh');
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout')->middleware([ApiAuthToken::class]);

    Route::prefix('password')->name('password.')->group(function () {
        Route::post('/forgot', [PasswordController::class, 'forgot'])->name('forgot');
        Route::post('/reset', [PasswordController::class, 'reset'])->name('reset');
    });

    Route::middleware([ApiAuthToken::class])->group(function () {
        $roleMenuConfig = ValueUtil::getList('menu.role_menu');

        // menu_id = 1, menu_name = User List
        Route::middleware([ApiAuthorizeAccess::class . ':' . $roleMenuConfig['user']['menu_id']])->group(function () {
            Route::middleware([ApiAuthorizeCheckPermission::class . ':REGISTER'])->group(function() {
                // Route::prefix('user')->name('user.')->group(function () {
                //     Route::get('/list', [UserController::class, 'list'])->name('list');
                //     Route::get('/detail/{userId}', [UserController::class, 'detail'])->name('detail');
                //     Route::post('/create', [UserController::class, 'create'])->name('create');
                //     Route::post('/update/{userId}', [UserController::class, 'update'])->name('update');
                //     Route::post('/delete/{userId}', [UserController::class, 'delete'])->name('delete');
                // });
            });
        });

        // menu_id = 2, menu_name = Role List
        Route::middleware([ApiAuthorizeAccess::class . ':' . $roleMenuConfig['role']['menu_id']])->group(function () {
            Route::middleware([ApiAuthorizeCheckPermission::class . ':REGISTER'])->group(function() {
                // Route::prefix('role')->name('role.')->group(function () {
                //     Route::get('/list', [UserRoleController::class, 'list'])->name('list');
                //     Route::get('/detail/{userRoleId}', [UserRoleController::class, 'detail'])->name('detail');
                //     Route::post('/create', [UserRoleController::class, 'create'])->name('create');
                //     Route::post('/update/{userRoleId}', [UserRoleController::class, 'update'])->name('update');
                //     Route::post('/delete/{userRoleId}', [UserRoleController::class, 'delete'])->name('delete');
                // });
            });
        });
    });
});

Route::fallback(function (Request $request) {
    return ApiBusUtil::preBuiltErrorResponse(ApiCodeNo::URL_NOT_EXISTS);
});
