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
Route::resource('logs', 'UserLog\LogController', ['except' => ['create', 'edit']]);
Route::resource('workAreas', 'WorkArea\WorkAreaController', ['except' => ['create', 'edit']]);
Route::resource('staff', 'Staff\StaffController', ['except' => ['create', 'edit']]);
Route::resource('documentClients', 'ClientDocument\DocumentClientController', ['except' => ['create', 'edit']]);
Route::resource('clients', 'Clients\ClientsController', ['except' => ['create', 'edit']]);
Route::resource('documentClients', 'ClientDocument\DocumentClientController', ['except' => ['create', 'edit']]);
Route::resource('documentClients', 'ClientDocument\DocumentClientController', ['except' => ['create', 'edit']]);
Route::resource('phasesProcesses', 'PhasesProcess\PhasesProcessController', ['except' => ['create', 'edit']]);
Route::resource('processes', 'Process\ProcessController', ['except' => ['create', 'edit']]);
Route::resource('projects', 'Projects\ProjectsController', ['except' => ['create', 'edit']]);
Route::resource('detailProcesses', 'DetailProject\DetailProcessController', ['except' => ['create', 'edit']]);
Route::resource('projectStaffs', 'ProjectStaff\ProjectStaffController', ['except' => ['create', 'edit']]);
Route::resource('processProjects', 'ProcessProject\ProcessProjectController', ['except' => ['create', 'edit']]);
Route::resource('detailProjectProcessProjects', 'DetailProjectProcessProject\DetailProjectProcessProjectController', ['except' => ['create', 'edit']]);
Route::resource('documents', 'Document\DocumentController', ['except' => ['create', 'edit']]);
Route::resource('clientDocuments', 'ClientDocument\ClientDocumentController', ['except' => ['create', 'edit']]);
