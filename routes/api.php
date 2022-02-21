<?php

use App\Http\Controllers\AdminsController as AdminsAPI;
use App\Http\Controllers\CitiesController as CitiesAPI;
use App\Http\Controllers\CountriesController as CountriesAPI;
use App\Http\Controllers\CrewsController as CrewsAPI;
use App\Http\Controllers\GoodsTypesController as GoodsTypesAPI;
use App\Http\Controllers\MaintenancesController as MaintenancesAPI;
use App\Http\Controllers\OffersController as OffersAPI;
use App\Http\Controllers\OffersResponsesController as OffersResponsesAPI;
use App\Http\Controllers\OfficesController as OfficesAPI;
use App\Http\Controllers\OwnersController as OwnersAPI;
use App\Http\Controllers\PortsController as PortsAPI;
use App\Http\Controllers\RequestsController as RequestsAPI;
use App\Http\Controllers\RequestsResponsesController as RequestsResponsesAPI;
use App\Http\Controllers\RolesController as RolesAPI;
use App\Http\Controllers\TenantsController as TenantsAPI;
use App\Http\Controllers\UsersController as UsersAPI;
use App\Http\Controllers\VesselsController as VesselsAPI;
use App\Http\Controllers\VesselsTypesController as VesselsTypesAPI;
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
        Route::post('/update', [AdminsAPI::class, 'update']);
        Route::delete('/{id}', [AdminsAPI::class, 'delete']);
    });

    Route::group(['prefix' => 'owners'], function () {
        Route::get('/list', [OwnersAPI::class, 'list_api']);
        Route::post('/add', [OwnersAPI::class, 'add']);
        Route::post('/update', [OwnersAPI::class, 'update']);
        Route::put('/status/{id}', [OwnersAPI::class, 'status']);
        Route::delete('/{id}', [OwnersAPI::class, 'delete']);
    });

    Route::group(['prefix' => 'tenants'], function () {
        Route::get('/list', [TenantsAPI::class, 'list_api']);
        Route::post('/add', [TenantsAPI::class, 'add']);
        Route::post('/update', [TenantsAPI::class, 'update']);
        Route::put('/status/{id}', [TenantsAPI::class, 'status']);
        Route::delete('/{id}', [TenantsAPI::class, 'delete']);
    });

    Route::group(['prefix' => 'offices'], function () {
        Route::get('/list', [OfficesAPI::class, 'list_api']);
        Route::post('/add', [OfficesAPI::class, 'add']);
        Route::post('/update', [OfficesAPI::class, 'update']);
        Route::put('/status/{id}', [OfficesAPI::class, 'status']);
        Route::delete('/{id}', [OfficesAPI::class, 'delete']);
    });

    Route::group(['prefix' => 'owners'], function () {
        Route::get('/list', [OwnersAPI::class, 'list_api']);
        Route::post('/add', [OwnersAPI::class, 'add']);
        Route::post('/update', [OwnersAPI::class, 'update']);
        Route::put('/status/{id}', [OwnersAPI::class, 'status']);
        Route::delete('/{id}', [OwnersAPI::class, 'delete']);
    });

    Route::group(['prefix' => 'ports'], function () {
        Route::get('/list', [PortsAPI::class, 'list_api']);
        Route::post('/add', [PortsAPI::class, 'add']);
        Route::post('/update', [PortsAPI::class, 'update']);
        Route::put('/status/{id}', [PortsAPI::class, 'status']);
        Route::delete('/{id}', [PortsAPI::class, 'delete']);
    });

    Route::group(['prefix' => 'vessels-types'], function () {
        Route::get('/list', [VesselsTypesAPI::class, 'list_api']);
        Route::post('/add', [VesselsTypesAPI::class, 'add']);
        Route::post('/update', [VesselsTypesAPI::class, 'update']);
        Route::delete('/{id}', [VesselsTypesAPI::class, 'delete']);
    });

    Route::group(['prefix' => 'goods-types'], function () {
        Route::get('/list', [GoodsTypesAPI::class, 'list_api']);
        Route::post('/add', [GoodsTypesAPI::class, 'add']);
        Route::post('/update', [GoodsTypesAPI::class, 'update']);
        Route::delete('/{id}', [GoodsTypesAPI::class, 'delete']);
    });

    Route::group(['prefix' => 'crews'], function () {
        Route::get('/list/{id?}', [CrewsAPI::class, 'list_api']);
        Route::post('/add', [CrewsAPI::class, 'add']);
        Route::post('/update', [CrewsAPI::class, 'update']);
        Route::put('/status/{id}', [CrewsAPI::class, 'status']);
        Route::delete('/{id}', [CrewsAPI::class, 'delete']);
    });

    Route::group(['prefix' => 'maintenances'], function () {
        Route::get('/list/{id?}', [MaintenancesAPI::class, 'list_api']);
        Route::post('/add', [MaintenancesAPI::class, 'add']);
        Route::post('/update', [MaintenancesAPI::class, 'update']);
        Route::put('/status/{id}', [MaintenancesAPI::class, 'status']);
        Route::delete('/{id}', [MaintenancesAPI::class, 'delete']);
    });

    Route::group(['prefix' => 'vessels'], function () {
        Route::get('/list', [VesselsAPI::class, 'list_api']);
        Route::post('/add', [VesselsAPI::class, 'add']);
        Route::post('/update', [VesselsAPI::class, 'update']);
        Route::put('/status/{id}', [VesselsAPI::class, 'status']);
        Route::delete('/{id}', [VesselsAPI::class, 'delete']);
    });

    Route::group(['prefix' => 'requests'], function () {
        Route::get('/list', [RequestsAPI::class, 'list_api']);
        Route::post('/add', [RequestsAPI::class, 'add']);
        Route::post('/update', [RequestsAPI::class, 'update']);
        Route::put('/status/{id}', [RequestsAPI::class, 'status']);
        Route::delete('/{id}', [RequestsAPI::class, 'delete']);
    });

    Route::group(['prefix' => 'offers'], function () {
        Route::get('/list', [OffersAPI::class, 'list_api']);
        Route::post('/add', [OffersAPI::class, 'add']);
        Route::post('/update', [OffersAPI::class, 'update']);
        Route::put('/status/{id}', [OffersAPI::class, 'status']);
        Route::delete('/{id}', [OffersAPI::class, 'delete']);
    });

    Route::group(['prefix' => 'admins'], function () {
        Route::get('/list', [AdminsAPI::class, 'list_api']);
        Route::post('/add', [AdminsAPI::class, 'add']);
        Route::post('/update', [AdminsAPI::class, 'update']);
        Route::put('/status/{id}', [AdminsAPI::class, 'status']);
        Route::delete('/{id}', [AdminsAPI::class, 'delete']);
    });

    Route::group(['prefix' => 'roles'], function () {
        Route::get('/list', [RolesAPI::class, 'list_api']);
        Route::post('/add', [RolesAPI::class, 'add'])->name('admin.roles.add');
        Route::post('/update', [RolesAPI::class, 'update']);
        Route::put('/status/{id}', [RolesAPI::class, 'status']);
        Route::delete('/{id}', [RolesAPI::class, 'delete']);
    });

    Route::group(['prefix' => 'requests_responses'], function () {
        Route::get('/list', [RequestsResponsesAPI::class, 'list_api']);
        Route::post('/add', [RequestsResponsesAPI::class, 'add']);
        Route::post('/update', [RequestsResponsesAPI::class, 'update']);
        Route::put('/status/{id}', [RequestsResponsesAPI::class, 'status']);
        Route::delete('/{id}', [RequestsResponsesAPI::class, 'delete']);
    });

    Route::group(['prefix' => 'offers_responses'], function () {
        Route::get('/list', [OffersResponsesAPI::class, 'list_api']);
        Route::post('/add', [OffersResponsesAPI::class, 'add']);
        Route::post('/update', [OffersResponsesAPI::class, 'update']);
        Route::put('/status/{id}', [OffersResponsesAPI::class, 'status']);
        Route::delete('/{id}', [OffersResponsesAPI::class, 'delete']);
    });

    Route::group(['prefix' => 'users'], function () {
        Route::get('/list', [UsersAPI::class, 'list_api']);
        Route::post('/check_field', [UsersAPI::class, 'check_field']);
        Route::put('/status/{id}', [UsersAPI::class, 'status']);
        Route::put('/update', [UsersAPI::class, 'update']);
        Route::delete('/{id}', [UsersAPI::class, 'delete']);
    });
});
