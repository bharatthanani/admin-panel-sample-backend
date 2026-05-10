<?php

use App\Http\Controllers\Api\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\RolePermissionController;
use App\Http\Controllers\Api\Admin\ProductController;
use App\Http\Controllers\Api\Admin\StoreController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware(['auth:api'])->group(function () {

    Route::get('/me/permissions', [RolePermissionController::class, 'myPermissions']);

    // Route::middleware('role:admin')->group(function () {

        // Roles CRUD
        Route::get('/roles',           [RolePermissionController::class, 'getRoles']);
        Route::post('/roles',          [RolePermissionController::class, 'createRole']);
        Route::put('/roles/{id}',      [RolePermissionController::class, 'updateRole']);
        Route::delete('/roles/{id}',   [RolePermissionController::class, 'deleteRole']);

        // Permissions list
        Route::get('/permissions',     [RolePermissionController::class, 'getPermissions']);

        // Users with role assignment
        Route::get('/users',                        [RolePermissionController::class, 'getUsers']);
        Route::post('/users/{id}/assign-role',      [RolePermissionController::class, 'assignRole']);
    // });

    Route::prefix('user')->group(function () {
        Route::get('get-users',[UserController::class,'getUsers']);
        Route::get('get-vendors',[UserController::class,'getVendors']);
        Route::get('delete-user/{id}', [UserController::class, 'deleteUser']);
        Route::post('add-user', [UserController::class, 'addUser']);
        Route::post('update-user/{id}', [UserController::class, 'updateUser']);
    });
    Route::prefix('product')->group(function () {
        Route::get('get-colors-backend',[ProductController::class,'getColorsForBackend']);
        Route::get('get-categories-backend',[ProductController::class,'getCategoryBackend']);
        Route::post('add-product-backend',[ProductController::class,'addProductBackend']);
        Route::get('get-product-backend',[ProductController::class,'getProductBackend']);
        Route::get('delete-product-backend/{id}',[ProductController::class,'deleteProductBackend']);
        Route::post('add-update-category-backend',[ProductController::class,'addUpdateCategoryBackend']);
        Route::get('delete-category-backend/{id}',[ProductController::class,'deleteCategoryBackend']);
    });
     
     Route::prefix('store')->group(function () {
            Route::resource('stores', StoreController::class);
            Route::get('get-store-tags',[StoreController::class,'getStoreTags']);
            Route::post('add-store',[StoreController::class,'addStore']);
            Route::get('delete-store/{id}',[StoreController::class,'deleteStore']);
            Route::post('update-store/{id}',[StoreController::class,'updateStore']);
            Route::get('get-stores-backend',[StoreController::class,'getStoresBackend']);
            Route::get('get-store-category-backend/{id}',[StoreController::class,'getStoreCategoryBackend']);
            Route::get('stores/{storeId}/products', [StoreController::class, 'getStoreProducts']);
     });

});

Route::post('/login', [AuthController::class, 'login']);
Route::post("create-account",[UserController::class,'createAccount'])->name("create-account");









