<?php

use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::resource('carExamples', 'CarExample\CarExampleController', ['except' => ['create', 'edit']]);
Route::resource('rols', 'Rol\RolController', ['except' => ['create', 'edit']]);
Route::resource('roles', 'Role\RoleController', ['except' => ['create', 'edit']]);
Route::resource('logs', 'Log\LogController', ['except' => ['create', 'edit']]);
Route::resource('workAreas', 'WorkArea\WorkAreaController', ['except' => ['create', 'edit']]);
Route::resource('staff', 'Staff\StaffController', ['except' => ['create', 'edit']]);
Route::resource('documentClients', 'DocumentClient\DocumentClientController', ['except' => ['create', 'edit']]);
Route::resource('clients', 'Clients\ClientsController', ['except' => ['create', 'edit']]);
