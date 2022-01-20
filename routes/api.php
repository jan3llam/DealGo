<?php

use App\Http\Controllers\AdminsController as AdminsAPI;
use App\Http\Controllers\CitiesController as CitiesAPI;
use App\Http\Controllers\CountriesController as CountriesAPI;
use App\Http\Controllers\OwnersController as OwnersAPI;
use App\Http\Controllers\UsersController as UsersAPI;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::group(['prefix' => 'user', 'middleware' => ['api.logger']], function () {
//
//    Route::group(['prefix' => 'authentication'], function () {
//        Route::post('/signIn', [AuthController::class, 'signIn']);
//        Route::post('/signUp', [AuthController::class, 'signUp']);
//        Route::post('/resendCode', [AuthController::class, 'sendCode']);
//        Route::post('/forgetPassword', [AuthController::class, 'sendCode']);
//        Route::post('/resetPassword', [AuthController::class, 'resetPassword']);
//        Route::post('/refreshToken', [AuthController::class, 'refresh']);
//        Route::group(['middleware' => ['auth:sanctum']], function () {
//            Route::post('/signOut', [AuthController::class, 'signOut']);
//        });
//    });
//
//    Route::group(['prefix' => 'profile', 'middleware' => 'auth:sanctum'], function () {
//        Route::get('/get', [ProfileController::class, 'getProfile']);
//        Route::post('/registerFCMToken', [ProfileController::class, 'registerFCMToken']);
//        Route::put('/updateProfile', [ProfileController::class, 'updateProfile']);
//        Route::put('/uploadImage', [ProfileController::class, 'uploadImage']);
//        Route::put('/changePassword', [ProfileController::class, 'changePassword']);
//        Route::get('/get/{id}', [ProfileController::class, 'getProfile']);
//        Route::get('/getNotifications', [ProfileController::class, 'getNotifications']);
//        Route::put('/notification/{id}', [ProfileController::class, 'getNotification']);
//    });
//
//    Route::group(['prefix' => 'invite', 'middleware' => ['auth:sanctum']], function () {
//        Route::post('/inviteEmail', [InviteController::class, 'inviteByEmail']);
//    });
//
//    Route::group(['prefix' => 'search'], function () {
//        Route::get('/provider', [SearchController::class, 'searchProviders']);
//    });
//
//    Route::group(['prefix' => 'content'], function () {
//        Route::get('/get/{key}', [ContentController::class, 'getContent']);
//        Route::get('/getCities/{id?}', [CitiesController::class, 'getCities']);
//        Route::get('/getRegions', [RegionsController::class, 'getRegions']);
//        Route::get('/getSpecifications', [SpecificationsController::class, 'getSpecifications']);
//        Route::get('/getSlider', [ContentController::class, 'getSlider']);
//        Route::get('/getSettings', [ContentController::class, 'getSettings']);
//    });
//
//    Route::group(['prefix' => 'tickets', 'middleware' => ['auth:sanctum']], function () {
//        Route::post('/add', [TicketsController::class, 'add']);
//        Route::get('/list', [TicketsController::class, 'list']);
//        Route::get('/{id}', [TicketsController::class, 'get']);
//        Route::delete('/{id}', [TicketsController::class, 'delete']);
//    });
//
//    Route::group(['prefix' => 'relations', 'middleware' => ['auth:sanctum']], function () {
//        Route::post('/like/{id}', [RelationsController::class, 'like']);
//        Route::put('/dislike/{id}', [RelationsController::class, 'dislike']);
//        Route::get('/getMyLikes', [RelationsController::class, 'getMyLikes']);
//    });
//
//    Route::group(['prefix' => 'coupons'], function () {
//        Route::get('/list', [CouponsController::class, 'list']);
//        Route::get('/{id}', [CouponsController::class, 'get']);
//    });
//
//    Route::group(['prefix' => 'providers',], function () {
//        Route::get('/list', [ProvidersController::class, 'list']);
//        Route::get('/{id}', [ProvidersController::class, 'get']);
//    });
//
//    Route::group(['prefix' => 'packages',], function () {
//        Route::get('/list', [PackagesController::class, 'list']);
//        Route::get('/{id}', [PackagesController::class, 'get']);
//    });
//});

Route::group(['prefix' => 'admin'], function () {

    Route::group(['prefix' => 'countries'], function () {
        Route::get('/list', [CountriesAPI::class, 'list_api']);
    });

    Route::group(['prefix' => 'cities'], function () {
        Route::get('/list/{id}', [CitiesAPI::class, 'getCities']);
    });

    Route::group(['prefix' => 'admins'], function () {
        Route::get('/list', [AdminsAPI::class, 'list_api']);
        Route::post('/add', [AdminsAPI::class, 'add']);
        Route::put('/status/{id}', [AdminsAPI::class, 'status']);
        Route::put('/update/{id}', [AdminsAPI::class, 'update']);
        Route::delete('/{id}', [AdminsAPI::class, 'delete']);
    });

    Route::group(['prefix' => 'owners'], function () {
        Route::get('/list', [OwnersAPI::class, 'list_api']);
        Route::post('/add', [OwnersAPI::class, 'add']);
        Route::post('/update/{id}', [OwnersAPI::class, 'update']);
        Route::put('/status/{id}', [OwnersAPI::class, 'status']);
        Route::delete('/{id}', [OwnersAPI::class, 'delete']);
    });

    Route::group(['prefix' => 'users'], function () {
        Route::get('/list', [UsersAPI::class, 'list_api']);
        Route::put('/status/{id}', [UsersAPI::class, 'status']);
        Route::put('/update/{id}', [UsersAPI::class, 'update']);
        Route::delete('/{id}', [UsersAPI::class, 'delete']);
    });
});
