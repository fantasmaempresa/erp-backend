<?php

use App\Http\Controllers\Appendant\AppendantController;
use App\Http\Controllers\Auth\AuthActionController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryOperation\CategoryOperationController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\ClientDocument\ClientDocumentController;
use App\Http\Controllers\ClientLink\ClientLinkActionController;
use App\Http\Controllers\ClientLink\ClientLinkController;
use App\Http\Controllers\Concept\ConceptController;
use App\Http\Controllers\Deduction\DeductionController;
use App\Http\Controllers\DetailProject\DetailProjectController;
use App\Http\Controllers\DisposalRealEstate\DisposalRealEstateController;
use App\Http\Controllers\Document\DocumentController;
use App\Http\Controllers\Document\DocumentLinkController;
use App\Http\Controllers\ExtraHour\ExtraHourController;
use App\Http\Controllers\FormStructure\FromStructureController;
use App\Http\Controllers\GeneralTemplate\GeneralTemplateController;
use App\Http\Controllers\Grantor\GrantorController;
use App\Http\Controllers\GrantorLink\GrantorLinkController;
use App\Http\Controllers\InversionUnit\InversionUnitController;
use App\Http\Controllers\IsoDocument\IsoDocumentController;
use App\Http\Controllers\NationalConsumerPriceIndex\NationalConsumerPriceIndexController;
use App\Http\Controllers\Notification\NotificationController;
use App\Http\Controllers\Notification\NotificationFilterController;
use App\Http\Controllers\Operation\OperationController;
use App\Http\Controllers\Operation\OperationFilterController;
use App\Http\Controllers\Perception\PerceptionController;
use App\Http\Controllers\PhasesProcess\PhasesProcessController;
use App\Http\Controllers\Place\PlaceController;
use App\Http\Controllers\Procedure\ProcedureActionController;
use App\Http\Controllers\Procedure\ProcedureController;
use App\Http\Controllers\Procedure\ProcedureFilterController;
use App\Http\Controllers\Procedure\ProcedureGraphicController;
use App\Http\Controllers\Procedure\ProcedureValidatorsController;
use App\Http\Controllers\Procedure\RegistrationProcedureDataController;
use App\Http\Controllers\ProcedureComment\ProcedureCommentController;
use App\Http\Controllers\Process\ProcessController;
use App\Http\Controllers\ProcessingIncome\ProcessingIncomeController;
use App\Http\Controllers\ProcessingIncomeComment\ProcessingIncomeCommentController;
use App\Http\Controllers\ProcessProject\ProcessProjectController;
use App\Http\Controllers\Project\ProjectActionController;
use App\Http\Controllers\Project\ProjectController;
use App\Http\Controllers\Project\ProjectFilterController;
use App\Http\Controllers\ProjectQuote\ProjectQuoteController;
use App\Http\Controllers\ProjectQuote\ProjectQuoteFilterController;
use App\Http\Controllers\ProjectQuote\ProjectQuoteOperationsController;
use App\Http\Controllers\ProjectQuote\ProjectQuoteReportController;
use App\Http\Controllers\ProjectStaff\ProjectStaffController;
use App\Http\Controllers\Rate\RateController;
use App\Http\Controllers\Role\RoleActionController;
use App\Http\Controllers\Role\RoleController;
use App\Http\Controllers\Salary\SalaryController;
use App\Http\Controllers\Shape\ShapeActionController;
use App\Http\Controllers\Shape\ShapeController;
use App\Http\Controllers\Staff\StaffController;
use App\Http\Controllers\Stake\StakeController;
use App\Http\Controllers\StatusQuote\StatusQuoteController;
use App\Http\Controllers\TaxDatum\TaxDatumController;
use App\Http\Controllers\TemplateQuotes\TemplateQuotesController;
use App\Http\Controllers\TemplateShape\TemplateShapeController;
use App\Http\Controllers\TypeDisposalOperation\TypeDisposalOperationController;
use App\Http\Controllers\Unit\UnitController;
use App\Http\Controllers\User\UserActionController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\UserLog\UserLogController;
use App\Http\Controllers\VulnerableOperation\VulnerableOperationController;
use App\Http\Controllers\WorkArea\WorkAreaController;
use App\Http\Controllers\Line\LineController;
use App\Http\Controllers\Article\ArticleController;
use App\Http\Controllers\Book\BookController;
use App\Http\Controllers\Document\DocumentActionControler;
use App\Http\Controllers\Folio\FolioActionController;
use App\Http\Controllers\Folio\FolioController;
use App\Http\Controllers\Inventory\InventoryController;
use App\Http\Controllers\Inventory\InventoryActionController;
use App\Http\Controllers\Inventory\InventoryFilterController;
use App\Http\Controllers\Warehouse\WarehouseController;
use App\Http\Controllers\OfficeSecurityMeasures\OfficeSecurityMeasuresController;
use App\Http\Controllers\OfficeSecurityMeasures\OfficeSecurityMeasuresFilterController;
use App\Http\Controllers\MovementTracking\MovementTrackingController;
use App\Http\Controllers\Procedure\ProcedureReportController;
use App\Http\Controllers\Project\ProjectActionPredefinedController;
use App\Http\Controllers\Reminder\ReminderActionController;
use App\Http\Controllers\Reminder\ReminderController;
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

    Route::resource('users', UserController::class, ['except' => ['create', 'edit']]);
    Route::post('user/assign', [UserController::class, 'assignUserToEntity']);
    Route::post('user/updateMyInfo', [UserActionController::class, 'updateMyInfoUser']);

    Route::resource('userLogs', UserLogController::class, ['except' => ['create', 'edit']]);
    Route::resource('staff', StaffController::class, ['except' => ['create', 'edit']]);
    Route::resource('workAreas', WorkAreaController::class, ['except' => ['create', 'edit']]);
    //Route::resource('clients', ClientController::class, ['except' => ['create', 'edit']]);
    Route::resource('phasesProcesses', PhasesProcessController::class, ['except' => ['create', 'edit']]);
    Route::resource('projectStaffs', ProjectStaffController::class, ['except' => ['create', 'edit']]);
    Route::resource('processProjects', ProcessProjectController::class, ['except' => ['create', 'edit']]);
    Route::resource('documents', DocumentController::class, ['except' => ['create', 'edit']]);
    Route::resource('documents', DocumentController::class, ['except' => ['create', 'edit']]);
    Route::resource('clientDocuments', ClientDocumentController::class, ['except' => ['create', 'edit']]);
    Route::resource('documentLink', DocumentLinkController::class, ['except' => ['create', 'edit']]);
    Route::post('documentLink/updateAlternative', [DocumentLinkController::class, 'updateAlternative']);
    Route::get('documentLink/filter/getAppendix', [DocumentActionControler::class, 'getAppendix']);
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
    Route::get('projects/action/complete/project/{project}/process/{process}', [ProjectActionController::class, 'completeProcessProject']);
    Route::post('projects/action/next/project/{project}/process/{process}', [ProjectActionController::class, 'nextPhaseProcess']);
    Route::post('projects/action/previous/project/{project}/process/{process}', [ProjectActionController::class, 'previousPhaseProcess']);
    Route::post('projects/action/supervision/project/{project}/process/{process}', [ProjectActionController::class, 'supervisionPhase']);
    Route::post('projects/action/saveForm/project/{project}/process/{process}', [ProjectActionController::class, 'saveDataFormPhase']);
    Route::post('projects/action/assign/project/{project}/projectQuote/{projectQuote}', [ProjectActionController::class, 'assignQuoteProject']);
    Route::get('projects/action/finish/project/{project}', [ProjectActionController::class, 'finishProject']);

    Route::get('projects/filter/myProjects', [ProjectFilterController::class, 'getMyProjects']);
    //TODO agregar más datos de configuración para el front, por si debe de poner el formulario para solo vista o dejar
    // que ingrese los datos
    Route::get('projects/filter/currentForm/project/{project}/process/{process}', [ProjectFilterController::class, 'getCurrentPhaseForm']);
    Route::get('projects/filter/resumeProcess/project/{project}/process/{process}', [ProjectFilterController::class, 'getResumeProject']);
    //PROJECT PREDEFINED ROUTES
    Route::post('projects/predefined/phase/execute/project/{project}/process/{process}', [ProjectActionPredefinedController::class, 'executePhase']);
    Route::get('projects/predefined/phase/getInfo/project/{project}/process/{process}', [ProjectActionPredefinedController::class, 'getInfoProject']);
    Route::post('projects/predefined/phase/getStructureFormat/project/{project}/process/{process}', [ProjectActionPredefinedController::class, 'getStructureFormat']);
    Route::post('projects/predefined/phase/getFormat', [ProjectActionPredefinedController::class, 'getReportFormat']);
    Route::post('projects/predefined/phase/project/{project}/process/{process}/format', [ProjectActionPredefinedController::class, 'saveFormat']);
    Route::post('projects/predefined/phase/project/{project}/process/{process}/format/{reportConfiguration}/update', [ProjectActionPredefinedController::class, 'updateFormat']);
    Route::post('projects/predefined/phase/project/{project}/process/{process}/getLastedRelatedReports', [ProjectActionPredefinedController::class, 'getLastedRelatedReportsFromReport']);
    Route::post('projects/predefined/phase/project/{project}/process/{process}/getLastedReports', [ProjectActionPredefinedController::class, 'getLastedReportsFromProjects']);
    Route::post('projects/predefined/phase/project/{project}/process/{process}/issueEventToPhase', [ProjectActionPredefinedController::class, 'issueEventToPhase']);

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
    Route::post('projectQuote/getReport', [ProjectQuoteReportController::class, 'makePDF']);


    //ROUTES PAYROLL3
    Route::resource('salaries', SalaryController::class, ['except' => ['create', 'edit']]);
    Route::resource('taxData', TaxDatumController::class, ['except' => ['create', 'edit']]);
    Route::resource('perceptions', PerceptionController::class, ['except' => ['create', 'edit']]);
    Route::resource('deductions', DeductionController::class, ['except' => ['create', 'edit']]);
    Route::resource('extraHours', ExtraHourController::class, ['except' => ['create', 'edit']]);
    //Route::resource('disabilities',DisabilityController::class, ['except' => ['create', 'edit']]);

    //ROUTE NOTARY
    Route::resource('shape', ShapeController::class, ['except' => ['create', 'edit']]);
    Route::resource('stake', StakeController::class, ['except' => ['create', 'edit']]);
    Route::resource('templateShape', TemplateShapeController::class, ['except' => ['create', 'edit']]);
    Route::resource('operations', OperationController::class, ['except' => ['create', 'edit']]);

    Route::post('operations/filter/documents', [OperationFilterController::class, 'documentsOperation']);
    
    Route::resource('procedures', ProcedureController::class, ['except' => ['create', 'edit']]);
    Route::resource('registrationProcedureData', RegistrationProcedureDataController::class, ['except' => ['create', 'edit', 'update']]);
    Route::post('registrationProcedureData/updateAlternative/{registrationProcedureData}', [RegistrationProcedureDataController::class, 'update']);
    Route::resource('grantors', GrantorController::class, ['except' => ['create', 'edit']]);
    Route::resource('places', PlaceController::class, ['except' => ['create', 'edit']]);
    Route::resource('isoDocumentation', IsoDocumentController::class, ['except' => ['create', 'edit']]);
    Route::resource('book', BookController::class);
    Route::resource('folio', FolioController::class);

    //GENERATOR REPORTS
    Route::get('report/generator/procedure/shape/{shape}', [ShapeActionController::class, 'generateShape']);
    //ROUTE NOTARY VALIDATORS
    Route::get('procedure/validator/uniqueValue/{name}', [ProcedureValidatorsController::class, 'uniqueValueValidator']);
    Route::get('procedure/validator/uniqueInstrumentValue/{name}', [ProcedureValidatorsController::class, 'uniqueValueInstrumentValidator']);
    Route::get('procedure/validator/uniqueFolioValue/{folio}', [ProcedureValidatorsController::class, 'uniqueFolioValueValidator']);

    Route::get('procedure/filter/myProcedures', [ProcedureFilterController::class, 'myProcedures']);
    Route::get('procedure/filter/withoutData', [ProcedureFilterController::class, 'proceduresWithoutData']);
    Route::get('procedure/filter/vulnerableOperations', [ProcedureFilterController::class, 'proceduresVulnerableOperations']);
    Route::put('procedure/grantors/additionalData/{procedure}', [ProcedureActionController::class, 'grantorsAdditionalData']);
    
    Route::post('procedures/action/notPassedExpedient', [ProcedureActionController::class, 'notPassedExpedient']);

    Route::resource('clients', ClientController::class, ['except' => ['create', 'edit']]);

    //NATIONAL CONSUMER PRICE INDEX
    Route::resource('nationalConsumerPriceIndex', NationalConsumerPriceIndexController::class, ['except' => ['create', 'edit']]);

    //INVERSION UNIT
    Route::resource('inversionUnit', InversionUnitController::class, ['except' => ['create', 'edit']]);

    //APPENDANT 9
    Route::resource('appendant', AppendantController::class, ['only' => ['index', 'show', 'update']]);

    //RATE
    Route::resource('rate', RateController::class, ['except' => ['create', 'edit']]);

    //TYPE DISPOSAL OPERATION
    Route::resource('typeDisposalOperation', TypeDisposalOperationController::class, ['except' => ['create', 'edit']]);

    //DISPOSAL REAL ESTATE
    Route::resource('disposalRealEstate', DisposalRealEstateController::class, ['except' => ['create', 'edit']]);
    Route::get('disposalRealEstate/report/{disposalRealEstate}', [DisposalRealEstateController::class, 'generateReport']);


    //PROCEDURE COMMENT
    Route::resource('procedureComment', ProcedureCommentController::class, ['except' => ['create', 'edit']]);

    //CLIENT LINK ACTIONS
    Route::put('clientLinks/active/{clientLink}', [ClientLinkActionController::class, 'active']);

    //GENERAL TEMPLATE
    Route::resource('generalTemplate', GeneralTemplateController::class, ['except' => ['create', 'edit']]);

    Route::resource('processingIncome', ProcessingIncomeController::class, ['except' => ['create', 'edit']]);

    Route::resource('processingIncomeComment', ProcessingIncomeCommentController::class, ['except' => ['create', 'edit']]);

    Route::resource('categoryOperation', CategoryOperationController::class, ['except' => ['create', 'edit']]);

    Route::resource('unit', UnitController::class, ['except' => ['create', 'edit']]);

    Route::resource('vulnerableOperation', VulnerableOperationController::class, ['except' => ['create', 'edit']]);

    Route::resource('grantorLink', GrantorLinkController::class, ['except' => ['create', 'edit']]);

    //PROCEDURE GRAPHICS
    Route::get('procedure/graphics/registered', [ProcedureGraphicController::class, 'registredProcedures']);
    Route::get('procedure/graphics/withoutData', [ProcedureGraphicController::class, 'proceduresWithoutData']);
    Route::get('procedure/graphics/withoutShape', [ProcedureGraphicController::class, 'proceduresWithoutShape']);
    Route::get('procedure/graphics/withoutDocument', [ProcedureGraphicController::class, 'procedureWithoutDocument']);

    //INVENTORIES
    Route::resource('line', LineController::class);
    Route::resource('inventory',InventoryController::class);

    Route::post('inventory/action/initialInventory', [InventoryActionController::class, 'initialInventory']);
    Route::post('inventory/action/purchase', [InventoryActionController::class, 'purchase']);
    Route::post('inventory/action/sale', [InventoryActionController::class, 'sale']);
    Route::post('inventory/action/warehouseTransfer', [InventoryActionController::class, 'warehouseTransfer']);
    Route::get('inventory/filter/getWarehouseInventory', [InventoryFilterController::class , 'getWarehouseInventory']);
    
    Route::resource('article',ArticleController::class);
    Route::resource('warehouse', WarehouseController::class);
    Route::resource('movementTracking', MovementTrackingController::class);

    Route::resource('officeSecurityMeasures', OfficeSecurityMeasuresController::class);
    Route::get('officeSecurityMeasures/filter/getStaffOfficeSecurityMeasures', [OfficeSecurityMeasuresFilterController::class, 'getStaffOfficeSecurityMeasures']);

    //RECOMMENDATIONS INSTRUMENT AND FOLIO
    Route::get('procedures/recommendation/expedient', [ProcedureActionController::class, 'expedientRecommendation']);
    Route::get('folio/recommendation/instrument', [FolioActionController::class, 'instrumentRecommendation']);
    Route::get('folio/recommendation/folio', [FolioActionController::class, 'foliosRecommendation']);

    //CANCEL FOLIOS
    Route::put('folio/cancel/{folio}', [FolioActionController::class, 'cancelFolio']);

    //UNUSED FOLIOS
    Route::get('folio/unused/{book}', [FolioActionController::class, 'unusedFolios']);
    Route::get('folio/unused/count/{book}', [FolioActionController::class, 'foliosCount']);
    Route::get('folio/instrument/unused', [FolioActionController::class, 'unusedInstruments']);
    Route::get('folio/checkApendix/{folio}', [FolioActionController::class, 'checkApendix']);

    //FOLIO REPORT
    Route::get('procedure/report/controlFolio', [ProcedureReportController::class, 'folioContol']);
    Route::get('folio/procedure/unused/{folio}', [FolioActionController::class, 'unsetProcedure']);

    //REMINDERS 
    Route::resource('reminder', ReminderController::class);
    Route::get('reminder/enableDisable/{reminder}', [ReminderActionController::class, 'enable_disable_reminder']);
});


Route::group(['middleware' => ['auth:api']], function () {
    Route::get('oauth/logout', [AuthController::class, 'logoutApi']);
    Route::get('oauth/user/online', [AuthController::class, 'onlineUser']);
    Route::get('oauth/user/offline', [AuthController::class, 'offlineUser']);
    Route::get('oauth/user/locked/{user}', [AuthController::class, 'lockUser']);
    Route::get('oauth/user/unlocked/{user}', [AuthController::class, 'unlockUser']);
    Route::post('oauth/user/closeSystem/{user}', [AuthActionController::class, 'logoutUser']);

    Route::get('roles/modules/construct', [RoleActionController::class, 'constructMenu']);

    //NOTIFICATIONS ROUTES
    Route::resource('notifications', NotificationController::class, ['only' => ['index', 'show', 'update']]);
    Route::get('notifications/filter/getLastUserNotifications', [NotificationFilterController::class, 'getLastUserNotifications']);
    Route::get('notifications/filter/getUncheckUserNotifications', [NotificationFilterController::class, 'getUncheckUserNotifications']);
    Route::get('notifications/filter/getCheckUserNotifications', [NotificationFilterController::class, 'getCheckUserNotifications']);
});

//ROUTES OAUTH AND OPERATIONS LOGIN USERS
Route::post('oauth/token', [AuthController::class, 'issueToken']);
