<?php

use App\Http\Controllers\Auth\AuthActionController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\ClientDocument\ClientDocumentController;
use App\Http\Controllers\ClientLink\ClientLinkController;
use App\Http\Controllers\Concept\ConceptController;
use App\Http\Controllers\Deduction\DeductionController;
use App\Http\Controllers\DetailProject\DetailProjectController;
use App\Http\Controllers\DetailProjectProcessProject\DetailProjectProcessProjectController;
use App\Http\Controllers\Document\DocumentController;
use App\Http\Controllers\ExtraHour\ExtraHourController;
use App\Http\Controllers\FormStructure\FromStructureController;
use App\Http\Controllers\Notification\NotificationController;
use App\Http\Controllers\Notification\NotificationFilterController;
use App\Http\Controllers\Perception\PerceptionController;
use App\Http\Controllers\PhasesProcess\PhasesProcessController;
use App\Http\Controllers\Process\ProcessController;
use App\Http\Controllers\ProcessProject\ProcessProjectController;
use App\Http\Controllers\Project\ProjectActionController;
use App\Http\Controllers\Project\ProjectController;
use App\Http\Controllers\Project\ProjectFilterController;
use App\Http\Controllers\ProjectQuote\ProjectQuoteController;
use App\Http\Controllers\ProjectQuote\ProjectQuoteFilterController;
use App\Http\Controllers\ProjectQuote\ProjectQuoteOperationsController;
use App\Http\Controllers\ProjectStaff\ProjectStaffController;
use App\Http\Controllers\Role\RoleActionController;
use App\Http\Controllers\Role\RoleController;
use App\Http\Controllers\Salary\SalaryController;
use App\Http\Controllers\Staff\StaffController;
use App\Http\Controllers\StatusQuote\StatusQuoteController;
use App\Http\Controllers\TaxDatum\TaxDatumController;
use App\Http\Controllers\TemplateQuotes\TemplateQuotesController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\UserLog\UserLogController;
use App\Http\Controllers\WorkArea\WorkAreaController;
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

Route::group(['middleware' => ['auth:api', 'permission']], function () {
    Route::resource('roles', RoleController::class, ['except' => ['create', 'edit']]);
    Route::get('roles/modules/get', [RoleActionController::class, 'getModules']);
    Route::get('roles/modules/construct/{role}', [RoleActionController::class, 'constructMenu']);

    Route::resource('users', UserController::class, ['except' => ['create', 'edit']]);
    Route::resource('userLogs', UserLogController::class, ['except' => ['create', 'edit']]);
    Route::resource('staff', StaffController::class, ['except' => ['create', 'edit']]);
    Route::resource('workAreas', WorkAreaController::class, ['except' => ['create', 'edit']]);
//    Route::resource('clients', ClientController::class, ['except' => ['create', 'edit']]);
    Route::resource('phasesProcesses', PhasesProcessController::class, ['except' => ['create', 'edit']]);
    Route::resource('projectStaffs', ProjectStaffController::class, ['except' => ['create', 'edit']]);
    Route::resource('processProjects', ProcessProjectController::class, ['except' => ['create', 'edit']]);
    Route::resource('documents', DocumentController::class, ['except' => ['create', 'edit']]);
    Route::resource('clientDocuments', ClientDocumentController::class, ['except' => ['create', 'edit']]);
    Route::resource('concepts', ConceptController::class, ['except' => ['create', 'edit']]);
    Route::resource('statusQuotes', StatusQuoteController::class, ['except' => ['create', 'edit']]);
    Route::resource('clientLinks', ClientLinkController::class, ['except' => 'create', 'edit']);
    Route::resource('templateQuotes', TemplateQuotesController::class, ['except' => ['create', 'edit']]);
    Route::resource('processes', ProcessController::class, ['except' => ['create', 'edit']]);
    Route::resource('detailProject', DetailProjectController::class, ['except' => ['create', 'edit']]);
    Route::resource('formStructure', FromStructureController::class, ['except' => ['create', 'edit']]);


    //PROJECT PROJECTS ROUTES
    Route::resource('projects', ProjectController::class, ['except' => ['create', 'edit']]);
    Route::post('projects/action/start/project/{project}/process/{process}', [ProjectActionController::class, 'startProject']);
    Route::post('projects/action/next/project/{project}/process/{process}', [ProjectActionController::class, 'nextPhaseProcess']);
    Route::post('projects/action/previous/project/{project}/process/{process}', [ProjectActionController::class, 'previousPhaseProcess']);
    Route::post('projects/action/supervision/project/{project}/process/{process}', [ProjectActionController::class, 'supervisionPhase']);
    Route::post('projects/action/saveForm/project/{project}/process/{process}', [ProjectActionController::class, 'saveDataFormPhase']);

    Route::get('projects/filter/myProjects', [ProjectFilterController::class, 'getMyProjects']);
    Route::get('projects/filter/currentForm/project/{project}/process/{process}', [ProjectFilterController::class, 'getCurrentPhaseForm']);


    //PROJECT QUOTES ROUTES
    Route::resource('projectQuotes', ProjectQuoteController::class, ['except' => ['create', 'edit']]);
    Route::get('projectQuotes/filter/getQuotesStart', [ProjectQuoteFilterController::class, 'getQuotesStart']);
    Route::get('projectQuotes/filter/getQuotesReview', [ProjectQuoteFilterController::class, 'getQuotesReview']);
    Route::get('projectQuotes/filter/getQuotesApproved', [ProjectQuoteFilterController::class, 'getQuotesApproved']);
    Route::get('projectQuotes/filter/getQuotesFinish', [ProjectQuoteFilterController::class, 'getQuotesFinish']);
    Route::get('projectQuotes/filter/getQuotesByUser', [ProjectQuoteFilterController::class, 'getQuotesByUser']);
    Route::get('projectQuotes/filter/getQuotesUser', [ProjectQuoteFilterController::class, 'getQuotesUser']);
    Route::get('projectQuotes/filter/getQuotesByClient', [ProjectQuoteFilterController::class, 'getQuotesByClient']);
    Route::post('projectQuotes/calculate/reactive', [ProjectQuoteOperationsController::class, 'calculateReactiveProjectQuote']);


    //NOTIFICATIONS ROUTES
    Route::resource('notifications', NotificationController::class, ['only' => ['index', 'show', 'update']]);
    Route::get('notifications/filter/getLastUserNotifications', [NotificationFilterController::class, 'getLastUserNotifications']);
    Route::get('notifications/filter/getUncheckUserNotifications', [NotificationFilterController::class, 'getUncheckUserNotifications']);
    Route::get('notifications/filter/getCheckUserNotifications', [NotificationFilterController::class, 'getCheckUserNotifications']);


    //ROUTES PAYROLL3
    Route::resource('salaries', SalaryController::class, ['except' => ['create', 'edit']]);
    Route::resource('taxData', TaxDatumController::class, ['except' => ['create', 'edit']]);
    Route::resource('perceptions', PerceptionController::class, ['except' => ['create', 'edit']]);
    Route::resource('deductions', DeductionController::class, ['except' => ['create', 'edit']]);
    Route::resource('extraHours', ExtraHourController::class, ['except' => ['create', 'edit']]);
    //Route::resource('disabilities',DisabilityController::class, ['except' => ['create', 'edit']]);
});


Route::group(['middleware' => ['auth:api']], function () {
    Route::get('oauth/logout', [AuthController::class, 'logoutApi']);
    Route::get('oauth/user/online', [AuthController::class, 'onlineUser']);
    Route::get('oauth/user/offline', [AuthController::class, 'offlineUser']);
    Route::get('oauth/user/locked/{user}', [AuthController::class, 'lockUser']);
    Route::get('oauth/user/unlocked/{user}', [AuthController::class, 'unlockUser']);
    Route::post('oauth/user/closeSystem/{user}', [AuthActionController::class, 'logoutUser']);
});

//ROUTES OAUTH AND OPERATIONS LOGIN USERS
Route::post('oauth/token', [AuthController::class, 'issueToken']);
Route::resource('clients', ClientController::class, ['except' => ['create', 'edit']]);


