<?php

use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\ClientDocument\ClientDocumentController;
use App\Http\Controllers\Concept\ConceptController;
use App\Http\Controllers\ConceptProjectQuote\ConceptProjectQuoteController;
use App\Http\Controllers\Deduction\DeductionController;
use App\Http\Controllers\DetailProject\DetailProjectController;
use App\Http\Controllers\DetailProjectProcessProject\DetailProjectProcessProjectController;
use App\Http\Controllers\Disability\DisabilityController;
use App\Http\Controllers\Document\DocumentController;
use App\Http\Controllers\ExtraHour\ExtraHourController;
use App\Http\Controllers\Notification\NotificationController;
use App\Http\Controllers\Perception\PerceptionController;
use App\Http\Controllers\PhasesProcess\PhasesProcessController;
use App\Http\Controllers\Process\ProcessController;
use App\Http\Controllers\ProcessProject\ProcessProjectController;
use App\Http\Controllers\Project\ProjectController;
use App\Http\Controllers\ProjectQuote\ProjectQuoteController;
//use App\Http\Controllers\ProjectQuotes\ProjectQuotesController;
use App\Http\Controllers\ProjectStaff\ProjectStaffController;
use App\Http\Controllers\Role\RoleController;
use App\Http\Controllers\Salary\SalaryController;
use App\Http\Controllers\Staff\StaffController;
use App\Http\Controllers\StatusQuote\StatusQuoteController;
use App\Http\Controllers\TaxDatum\TaxDatumController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\UserLog\UserLogController;
use App\Http\Controllers\WorkArea\WorkAreaController;
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


//ROUTES PROJECTS
Route::resource('roles', RoleController::class, ['except' => ['create', 'edit']]);
Route::resource('users', UserController::class, ['except' => ['create', 'edit']]);
Route::resource('userLogs', UserLogController::class, ['except' => ['create', 'edit']]);
Route::resource('workAreas', WorkAreaController::class, ['except' => ['create', 'edit']]);
Route::resource('staff', StaffController::class, ['except' => ['create', 'edit']]);
Route::resource('clients', ClientController::class, ['except' => ['create', 'edit']]);
Route::resource('phasesProcesses', PhasesProcessController::class, ['except' => ['create', 'edit']]);
Route::resource('processes', ProcessController::class, ['except' => ['create', 'edit']]);
Route::resource('projects', ProjectController::class, ['except' => ['create', 'edit']]);
Route::resource('detailProject', DetailProjectController::class, ['except' => ['create', 'edit']]);
Route::resource('projectStaffs', ProjectStaffController::class, ['except' => ['create', 'edit']]);
Route::resource('processProjects', ProcessProjectController::class, ['except' => ['create', 'edit']]);
Route::resource('detailProjectProcessProjects', DetailProjectProcessProjectController::class, ['except' => ['create', 'edit']]);
Route::resource('documents', DocumentController::class, ['except' => ['create', 'edit']]);
Route::resource('clientDocuments', ClientDocumentController::class, ['except' => ['create', 'edit']]);

Route::resource('projectQuotes', ProjectQuoteController::class, ['except' => ['create', 'edit']])->middleware('auth:api');
Route::resource('statusQuotes', StatusQuoteController::class, ['except' => ['create', 'edit']]);
Route::resource('concepts', ConceptController::class, ['except' => ['create', 'edit']]);
Route::resource('notifications', NotificationController::class, ['only' => ['index', 'show']]);


//ROUTES PAYROLL
Route::resource('salaries', SalaryController::class, ['except' => ['create', 'edit']]);
Route::resource('taxData', TaxDatumController::class, ['except' => ['create', 'edit']]);
Route::resource('perceptions', PerceptionController::class, ['except' => ['create', 'edit']]);
Route::resource('deductions', DeductionController::class, ['except' => ['create', 'edit']]);
Route::resource('extraHours', ExtraHourController::class, ['except' => ['create', 'edit']]);
//Route::resource('disabilities',DisabilityController::class, ['except' => ['create', 'edit']]);

//ROUTES OAUTH
Route::post('oauth/token', 'Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');
