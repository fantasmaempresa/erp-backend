<?php
/*
 * CODE
 * Role Action Controller
*/

namespace App\Http\Controllers\Role;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Book\BookController;
use App\Htttp\Controllers\Article\ArticleController;
use App\Http\Controllers\CategoryOperation\CategoryOperationController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\ClientLink\ClientLinkController;
use App\Http\Controllers\Concept\ConceptController;
use App\Http\Controllers\DisposalRealEstate\DisposalRealEstateController;
use App\Http\Controllers\Document\DocumentController;
use App\Http\Controllers\Document\DocumentLinkController;
use App\Http\Controllers\Folio\FolioActionController;
use App\Http\Controllers\Folio\FolioController;
use App\Http\Controllers\GeneralTemplate\GeneralTemplateController;
use App\Http\Controllers\Grantor\GrantorController;
use App\Http\Controllers\GrantorLink\GrantorLinkController;
use App\Http\Controllers\InversionUnit\InversionUnitController;
use App\Http\Controllers\IsoDocument\IsoDocumentController;
use App\Http\Controllers\Inventory\InventoryController;
use App\Http\Controllers\Line\LineController;
use App\Http\Controllers\MovementTracking\MovementTrackingController;
use App\Http\Controllers\NationalConsumerPriceIndex\NationalConsumerPriceIndexController;
use App\Http\Controllers\OfficeSecurityMeasures\OfficeSecurityMeasuresController;
use App\Http\Controllers\Notification\NotificationController;
use App\Http\Controllers\Notification\NotificationFilterController;
use App\Http\Controllers\Operation\OperationController;
use App\Http\Controllers\Operation\OperationFilterController;
use App\Http\Controllers\Place\PlaceController;
use App\Http\Controllers\Procedure\ProcedureActionController;
use App\Http\Controllers\Procedure\ProcedureController;
use App\Http\Controllers\Procedure\ProcedureFilterController;
use App\Http\Controllers\Procedure\ProcedureGraphicController;
use App\Http\Controllers\Procedure\ProcedureReportController;
use App\Http\Controllers\Procedure\ProcedureValidatorsController;
use App\Http\Controllers\Procedure\RegistrationProcedureDataController;
use App\Http\Controllers\ProcedureComment\ProcedureCommentController;
use App\Http\Controllers\ProcessingIncome\ProcessingIncomeController;
use App\Http\Controllers\ProcessingIncomeComment\ProcessingIncomeCommentController;
use App\Http\Controllers\Project\ProjectActionController;
use App\Http\Controllers\Project\ProjectActionPredefinedController;
use App\Http\Controllers\Project\ProjectController;
use App\Http\Controllers\Project\ProjectFilterController;
use App\Http\Controllers\ProjectQuote\ProjectQuoteController;
use App\Http\Controllers\ProjectQuote\ProjectQuoteFilterController;
use App\Http\Controllers\ProjectQuote\ProjectQuoteOperationsController;
use App\Http\Controllers\Rate\RateController;
use App\Http\Controllers\Reminder\ReminderActionController;
use App\Http\Controllers\Reminder\ReminderController;
use App\Http\Controllers\Shape\ShapeController;
use App\Http\Controllers\Shape\ShapeActionController;
use App\Http\Controllers\Staff\StaffController;
use App\Http\Controllers\Stake\StakeController;
use App\Http\Controllers\TemplateQuotes\TemplateQuotesController;
use App\Http\Controllers\TemplateShape\TemplateShapeController;
use App\Http\Controllers\TypeDisposalOperation\TypeDisposalOperationController;
use App\Http\Controllers\Unit\UnitController;
use App\Http\Controllers\User\UserActionController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\UserFilterController;
use App\Http\Controllers\VulnerableOperation\VulnerableOperationController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\WorkArea\WorkAreaController;
use App\Models\ProcessingIncome;
use App\Models\Reminder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * @access  public
 *
 * @version 1.0
 */
class RoleActionController extends ApiController
{
    /**
     * @var array[]
     */
    public static $startMenu = [
        [
            'label' => 'Perfil',
            'route' => './profile',
            'icon' => 'account_circle',
            'controllers' => [
                UserFilterController::class,
                UserActionController::class,
                NotificationFilterController::class,
                NotificationController::class,
            ],
        ],
        [
            'label' => 'Catálogos',
            'icon' => 'folder_special',
            'route' => './disposal_assets',
            'dropdowns' => [
                [
                    'label' => 'Clientes',
                    'route' => './clients',
                    'icon' => 'people',
                ],
                [
                    'label' => 'Documentos',
                    'route' => './documents',
                    'icon' => 'document_scanner',
                ],
                [
                    'label' => 'Catálogo de Unidades',
                    'route' => './unit',
                    'icon' => 'groups',
                ],
            ],
            'controllers' => [
                ClientController::class,
                DocumentLinkController::class,
                ClientLinkController::class,
                DocumentController::class,
                UnitController::class,
            ],
        ],
        [
            'label' => 'Personal',
            'route' => './staff',
            'icon' => 'groups',
            'controllers' => [
                StaffController::class,
            ],
        ],
        [
            'label' => 'Áreas',
            'route' => './areas',
            'icon' => 'group_work',
            'controllers' => [
                WorkAreaController::class,
            ],
        ],
        [
            'label' => 'Cotabilidada',
            'route' => './quotes',
            'icon' => 'rule_folder',
            'dropdowns' => [
                [
                    'label' => 'Estados de la cotización',
                    'route' => './quote-statuses',
                    'icon' => 'group_work',
                ],
                [
                    'label' => 'Conceptos',
                    'route' => './concepts',
                    'icon' => 'group_work',
                ],
                [
                    'label' => 'Plantillas',
                    'route' => './project-quote-template',
                    'icon' => 'group_work',
                ],
                [
                    'label' => 'Nueva Cotización',
                    'route' => './project-quote/new',
                    'icon' => 'add_circle',
                ],
                [
                    'label' => 'Lista de cotizaciones',
                    'route' => './project-quote',
                    'icon' => 'group_work',
                ],
            ],
            'controllers' => [
                ProjectQuoteController::class,
                ProjectQuoteFilterController::class,
                ProjectQuoteOperationsController::class,
                TemplateQuotesController::class,
                ConceptController::class,
                ConceptController::class,
            ],
        ],
        [
            'label' => 'Adminitración de procesos',
            'icon' => 'rule_folder',
            'route' => './projects',
            'dropdowns' => [
                [
                    'label' => 'Actividades',
                    'route' => './process-phase',
                    'icon' => 'timeline',
                ],
                [
                    'label' => 'Procedimientos',
                    'route' => './process',
                    'icon' => 'pending_actions',
                ],
                [
                    'label' => 'Procesos',
                    'route' => './project',
                    'icon' => 'add_task',
                ],
                [
                    'label' => 'Procesos en curso',
                    'route' => './project-start',
                    'icon' => 'task_alt',
                ],
            ],
            'controllers' => [
                ProjectController::class,
                ProjectActionController::class,
                ProjectFilterController::class,
                ProjectActionPredefinedController::class,
                FolioController::class,
                FolioActionController::class
            ],
        ],
        [
            'label' => 'Procesos',
            'icon' => 'rule_folder',
            'route' => './projects',
            'dropdowns' => [
                [
                    'label' => 'Procesos en curso',
                    'route' => './project-start',
                    'icon' => 'task_alt',
                ],
            ],
            'controllers' => [
                ProjectController::class,
                ProjectActionController::class,
                ProjectFilterController::class,
                ProjectActionPredefinedController::class,
                FolioController::class,
                FolioActionController::class
            ],
        ],
        [
            'label' => 'Libro guía',
            'icon' => 'folder_special',
            'route' => './notary',
            'dropdowns' => [
                [
                    'label' => 'Categorías de operaciones',
                    'route' => './category-operation',
                    'icon' => 'folder',
                ],
                [
                    'label' => 'Libros',
                    'route' => './books',
                    'icon' => 'folder',
                ],
                [
                    'label' => 'Folios',
                    'route' => './folios',
                    'icon' => 'format_list_bulleted',
                ],
            ],
            'controllers' => [
                CategoryOperationController::class,
                OperationController::class,
                DocumentLinkController::class,
                DocumentController::class,
                BookController::class,
                FolioController::class,
            ],
        ],
        [
            'label' => 'Catálogos de procesos',
            'icon' => 'folder_special',
            'route' => './notary',
            'dropdowns' => [
                [
                    'label' => 'Operaciones',
                    'route' => './operations',
                    'icon' => 'format_list_bulleted',
                ],
                [
                    'label' => 'Participaciones',
                    'route' => './stakes',
                    'icon' => 'supervised_user_circle',
                ],
                [
                    'label' => 'Otorgantes',
                    'route' => './grantors',
                    'icon' => 'groups',
                ],
                [
                    'label' => 'Lugares',
                    'route' => './places',
                    'icon' => 'location_on',
                ],
            ],
            'controllers' => [
                OperationController::class,
                PlaceController::class,
                GrantorController::class,
                RegistrationProcedureDataController::class,
                StakeController::class,
                DocumentLinkController::class,
                DocumentController::class,
                GrantorLinkController::class,
                CategoryOperationController::class,
            ],
        ],
        [
            'label' => 'Trámites',
            'icon' => 'balance',
            'route' => './notary',
            'dropdowns' => [
                [
                    'label' => 'Formas',
                    'route' => './shapes',
                    'icon' => 'summarize',
                ],
                [
                    'label' => 'Trámites',
                    'route' => './procedures',
                    'icon' => 'event',
                ],
                [
                    'label' => 'Trámites con posible operación vulnerable',
                    'route' => './proceduresVulnerableOperations',
                    'icon' => 'campaign',
                ],
                [
                    'label' => 'Reportes',
                    'route' => './reports',
                    'icon' => 'summarize',
                ],
            ],
            'controllers' => [
                ShapeController::class,
                TemplateShapeController::class,
                OperationController::class,
                ProcedureController::class,
                PlaceController::class,
                GrantorController::class,
                RegistrationProcedureDataController::class,
                StakeController::class,
                DocumentLinkController::class,
                ClientController::class,
                RegistrationProcedureDataController::class,
                ProcedureCommentController::class,
                StaffController::class,
                ProcedureValidatorsController::class,
                ProcessingIncomeController::class,
                ProcessingIncomeCommentController::class,
                ShapeActionController::class,
                ProcedureFilterController::class,
                ProcedureActionController::class,
                FolioController::class,
                DocumentController::class,
                OperationFilterController::class,
                BookController::class,
                ProcedureReportController::class,
            ],
        ],
        [
            'label' => 'Calculadora Enagenación de bienes',
            'icon' => 'apartment',
            'route' => './disposal_assets',
            'dropdowns' => [
                [
                    'label' => 'Precio al Consumidor Nacional',
                    'route' => './nationalConsumer',
                    'icon' => 'attach_money',
                ],
                [
                    'label' => 'Unidad de inversión',
                    'route' => './inversionUnit',
                    'icon' => 'trending_up',
                ],
                [
                    'label' => 'Tasa',
                    'route' => './rate',
                    'icon' => 'savings',
                ],
                [
                    'label' => 'Operaciones vulnerables',
                    'route' => './vulnerableOperation',
                    'icon' => 'trending_down'
                ],
                [
                    'label' => 'Operaciones de eliminación',
                    'route' => './disposalOperation',
                    'icon' => 'money',
                ],
                [
                    'label' => 'Enajenación de Bienes',
                    'route' => './disposalRealEstate',
                    'icon' => 'monetization_on',
                ],
            ],
            'controllers' => [
                NationalConsumerPriceIndexController::class,
                InversionUnitController::class,
                RateController::class,
                TypeDisposalOperationController::class,
                DisposalRealEstateController::class,
            ],
        ],
        [
            'label' => 'Operaciones notariales vulnerables',
            'icon' => 'campaign',
            'route' => './vulnerableOperations',
            'dropdowns' => [
                [
                    'label' => 'Lista de operaciones vulnerables',
                    'route' => './vulnerableOperations',
                    'icon' => 'campaign',
                ],
                [
                    'label' => 'Completar Operación Vulnerable',
                    'route' => './vulnerableOperations/new',
                    'icon' => 'verified_user',
                ],
            ],
            'controllers' => [
                VulnerableOperationController::class,
                ProcedureController::class,
                StaffController::class,
            ],
        ],
        [
            'label' => 'Plantillas Generales',
            'icon' => 'description',
            'route' => './generalTemplates',
            'controllers' => [
                GeneralTemplateController::class
            ]
        ],
        [
            'label' => 'Documentación interna',
            'icon' => 'storage',
            'route' => './isoDocumentation',
            'controllers' => [
                IsoDocumentController::class
            ]
        ],
        [
            'label' => 'Inventario',
            'icon' => 'inventory_2',
            'route' => './inventories',
            'dropdowns' => [
                [
                    'label' => 'Línea',
                    'route' => './line',
                    'icon' => 'category',
                ],
                [
                    'label' => 'Artículo',
                    'route' => './article',
                    'icon' => 'dataset',
                ],
                [
                    'label' => 'Inventarios',
                    'route' => './inventory',
                    'icon' => 'inventory',
                ],
                [
                    'label' => 'Almacenes',
                    'route' => './warehouse',
                    'icon' => 'warehouse',
                ],
                [
                    'label' => 'Seguimiento de Movimientos',
                    'route' => './movementTracking',
                    'icon' => 'near_me',
                ],
                [
                    'label' => 'Medidas de Segurdad de la Oficina',
                    'route' => './officeSecurityMeasures',
                    'icon' => 'receipt',
                ],
            ],
            'controllers' => [
                ArticleController::class,
                InventoryController::class,
                LineController::class,
                MovementTrackingController::class,
                OfficeSecurityMeasuresController::class,
                WarehouseController::class,
            ],
        ],
        [
            'label' => 'Historial de notificaciones',
            'route' => './notifications/list',
            'icon' => 'history',
            'controllers' => [
                NotificationController::class,
            ],
        ],
        [
            'label' => 'Historial de recordatorios',
            'route' => './reminders',
            'icon' => 'history',
            'controllers' => [
                ReminderController::class,
                ReminderActionController::class,
                ProcedureController::class,
                ProcessingIncome::class,
            ],
        ],
        [
            'label' => 'Configuración',
            'icon' => 'settings',
            'route' => './settings',
            'dropdowns' => [
                [
                    'label' => 'Usuarios',
                    'route' => './users',
                    'icon' => 'person',
                ],
                [
                    'label' => 'Roles',
                    'route' => './roles',
                    'icon' => 'verified_user',
                ],
            ],
            'controllers' => [
                UserController::class,
                RoleController::class,
            ],
        ],
    ];
    /**
     * @return JsonResponse
     */
    public function getModules(): JsonResponse
    {
        $modules = [];
        foreach (self::$startMenu as $menu) {
            $modules[] = ['name' => $menu['label'], 'route' => $menu['route']];
        }

        return $this->showList($modules);
    }

    /**
     * @return JsonResponse
     */
    public function constructMenu(): JsonResponse
    {
        $menus = [
            'menuName' => 'Menu',
            'submenus' => [
                [
                    'label' => 'Home',
                    'route' => './dashboard',
                    'icon' => 'home',
                    'controllers' => [
                        ProcedureGraphicController::class,
                    ],
                ],
            ],
        ];

        $user = User::findOrFail(Auth::id());

        if ($user->role_id === Role::$ADMIN) {
            foreach (self::$startMenu as $menu) {
                unset($menu['controllers']);
                $menus['submenus'][] = $menu;
            }
        } else {
            foreach ($user->role->config['modules'] as $module) {
                foreach (self::$startMenu as $menu) {
                    if ($module['name'] === $menu['label']) {
                        unset($menu['controllers']);
                        $menus['submenus'][] = $menu;
                    }
                }
            }
        }

        return $this->showList($menus);
    }
}
