<?php

use App\Http\Controllers\AdminsController;
use App\Http\Controllers\ArticlesController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\ContractsController;
use App\Http\Controllers\CrewsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoodsTypesController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\MaintenancesController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\OffersController;
use App\Http\Controllers\OffersResponsesController;
use App\Http\Controllers\OfficesController;
use App\Http\Controllers\OwnersController;
use App\Http\Controllers\PortsController;
use App\Http\Controllers\RequestsController;
use App\Http\Controllers\RequestsResponsesController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ShipmentsController;
use App\Http\Controllers\TenantsController;
use App\Http\Controllers\TicketsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\VesselsController;
use App\Http\Controllers\VesselsTypesController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/', '/admin');

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {

    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'submit']);

    Route::group(['middleware' => 'auth:admins'], function () {
        Route::get('/', [DashboardController::class, 'home'])->name('home');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('home', [DashboardController::class, 'home'])->name('home');
        Route::get('content', [ContentController::class, 'view'])->name('content');
        Route::get('settings', [SettingsController::class, 'view'])->name('settings');
        Route::post('content', [ContentController::class, 'submit']);
        Route::post('settings', [SettingsController::class, 'submit']);
        Route::get('notification', [NotificationsController::class, 'list'])->name('notification');
        Route::post('notification', [NotificationsController::class, 'send']);
        Route::get('admins', [AdminsController::class, 'list'])->name('admins');
        Route::get('owners', [OwnersController::class, 'list'])->name('owners');
        Route::get('tenants', [TenantsController::class, 'list'])->name('tenants');
        Route::get('offices', [OfficesController::class, 'list'])->name('offices');
        Route::get('requests', [RequestsController::class, 'list'])->name('requests');
        Route::get('offers', [OffersController::class, 'list'])->name('offers');
        Route::get('ports', [PortsController::class, 'list'])->name('ports');
        Route::get('roles', [RolesController::class, 'list'])->name('roles');
        Route::get('requests_responses/{id?}', [RequestsResponsesController::class, 'list'])->name('requests_responses');
        Route::get('offers_responses/{id?}', [OffersResponsesController::class, 'list'])->name('offers_responses');
        Route::get('vessels', [VesselsController::class, 'list'])->name('vessels');
        Route::get('tickets', [TicketsController::class, 'list'])->name('tickets');
        Route::get('ticket/{id}', [TicketsController::class, 'view'])->name('ticket');
        Route::post('ticket/{id}', [TicketsController::class, 'reply'])->name('reply');
        Route::get('track', [VesselsController::class, 'track'])->name('vessels.track');
        Route::get('contracts', [ContractsController::class, 'list'])->name('contracts');
        Route::get('shipments', [ShipmentsController::class, 'list'])->name('shipments');
        Route::get('vtypes', [VesselsTypesController::class, 'list'])->name('vtypes');
        Route::get('gtypes', [GoodsTypesController::class, 'list'])->name('gtypes');
        Route::get('crews/{id?}', [CrewsController::class, 'list'])->name('crews');
        Route::get('maintenances/{id?}', [MaintenancesController::class, 'list'])->name('maintenances');
        Route::get('articles/{id?}', [ArticlesController::class, 'list'])->name('articles');
        Route::get('categories/{id?}', [CategoriesController::class, 'list'])->name('categories');
        Route::get('users', [UsersController::class, 'list'])->name('users');
        Route::get('user/{id}', [UsersController::class, 'view'])->name('user');
        Route::post('user/update', [UsersController::class, 'update'])->name('user.update');
    });
});

// locale Route
Route::get('lang/{locale}', [LanguageController::class, 'swap']);

//Route::group(['as' => 'frontend.'], function () {
//    Route::group(['middleware' => 'auth:web'], function () {
//        Route::get('/favorites', [FrontendController::class, 'favorites'])->name('favorites');
//        Route::get('/profile', [FrontendController::class, 'profile'])->name('profile');
//        Route::post('/profile', [FrontendController::class, 'updateProfile']);
//        Route::post('/password', [FrontendController::class, 'updatePassword'])->name('password');
//        Route::post('/ticket', [FrontendController::class, 'sendTicket'])->name('ticket');
//        Route::post('/like/{id}', [FrontendController::class, 'like'])->name('like');
//        Route::post('/dislike/{id}', [FrontendController::class, 'dislike'])->name('dislike');
//    });
//    Route::get('/', [FrontendController::class, 'home'])->name('home');
//    Route::get('/search', [FrontendController::class, 'search'])->name('search');
//    Route::get('/coupons', [FrontendController::class, 'couponsList'])->name('coupons');
//    Route::get('/providers', [FrontendController::class, 'providersList'])->name('providers');
//    Route::get('/static/{id}', [FrontendController::class, 'staticView'])->name('static');
//    Route::get('/coupon/{id}', [FrontendController::class, 'coupon'])->name('coupon');
//    Route::get('/provider/{id}', [FrontendController::class, 'provider'])->name('provider');
//    Route::get('/login', [FrontendController::class, 'login'])->name('login');
//    Route::get('/forget', [FrontendController::class, 'forget'])->name('forget');
//    Route::get('/restore', [FrontendController::class, 'restore'])->name('restore');
//    Route::post('/restore', [FrontendController::class, 'submitRestore']);
//    Route::post('/forget', [FrontendController::class, 'submitForget']);
//    Route::get('/signup', [FrontendController::class, 'signup'])->name('signup');
//    Route::get('/request', [FrontendController::class, 'request'])->name('request');
//    Route::post('/login', [FrontendController::class, 'submitLogin']);
//    Route::post('/signup', [FrontendController::class, 'submitSignup']);
//    Route::post('/request', [FrontendController::class, 'submitRequest']);
//    Route::get('/logout', [FrontendController::class, 'logout'])->name('logout');
//});
