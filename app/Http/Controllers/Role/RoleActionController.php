<?php
/*
 * CODE
 * Role Action Controller
*/

namespace App\Http\Controllers\Role;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\ClientLink\ClientLinkController;
use App\Http\Controllers\Concept\ConceptController;
use App\Http\Controllers\DisposalRealEstate\DisposalRealEstateController;
use App\Http\Controllers\Document\DocumentController;
use App\Http\Controllers\Document\DocumentLinkController;
use App\Http\Controllers\Grantor\GrantorController;
use App\Http\Controllers\InversionUnit\InversionUnitController;
use App\Http\Controllers\IsoDocument\IsoDocumentController;
use App\Http\Controllers\NationalConsumerPriceIndex\NationalConsumerPriceIndexController;
use App\Http\Controllers\Operation\OperationController;
use App\Http\Controllers\Place\PlaceController;
use App\Http\Controllers\Procedure\ProcedureController;
use App\Http\Controllers\Procedure\ProcedureValidatorsController;
use App\Http\Controllers\Procedure\RegistrationProcedureDataController;
use App\Http\Controllers\ProcedureComment\ProcedureCommentController;
use App\Http\Controllers\Project\ProjectActionController;
use App\Http\Controllers\Project\ProjectController;
use App\Http\Controllers\Project\ProjectFilterController;
use App\Http\Controllers\ProjectQuote\ProjectQuoteController;
use App\Http\Controllers\ProjectQuote\ProjectQuoteFilterController;
use App\Http\Controllers\ProjectQuote\ProjectQuoteOperationsController;
use App\Http\Controllers\Rate\RateController;
use App\Http\Controllers\Shape\ShapeController;
use App\Http\Controllers\Staff\StaffController;
use App\Http\Controllers\Stake\StakeController;
use App\Http\Controllers\TemplateQuotes\TemplateQuotesController;
use App\Http\Controllers\TemplateShape\TemplateShapeController;
use App\Http\Controllers\TypeDisposalOperation\TypeDisposalOperationController;
use App\Http\Controllers\User\UserActionController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\UserFilterController;
use App\Http\Controllers\WorkArea\WorkAreaController;
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
            ],
        ],
        [
            'label' => 'Clientes',
            'route' => './clients',
            'icon' => 'people',
            'controllers' => [
                ClientController::class,
                DocumentLinkController::class,
                ClientLinkController::class,
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
            'label' => 'Documentos',
            'route' => './documents',
            'icon' => 'document_scanner',
            'controllers' => [
                DocumentController::class,
            ],
        ],
        [
            'label' => 'Conceptos',
            'route' => './concepts',
            'icon' => 'group_work',
            'controllers' => [
                ConceptController::class,
            ],
        ],
        [
            'label' => 'Cotizaciones',
            'route' => './quotes',
            'icon' => 'rule_folder',
            'dropdowns' => [
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
                [
                    'label' => 'Estados de la cotización',
                    'route' => './quote-statuses',
                    'icon' => 'group_work',
                ],
                [
                    'label' => 'Plantillas',
                    'route' => './project-quote-template',
                    'icon' => 'group_work',
                ],
            ],
            'controllers' => [
                ProjectQuoteController::class,
                ProjectQuoteFilterController::class,
                ProjectQuoteOperationsController::class,
                TemplateQuotesController::class,
                ConceptController::class,
            ],
        ],
        [
            'label' => 'Proyectos',
            'icon' => 'rule_folder',
            'route' => './projects',
            'dropdowns' => [
                [
                    'label' => 'Fases',
                    'route' => './process-phase',
                    'icon' => 'timeline',
                ],
                [
                    'label' => 'Procesos',
                    'route' => './process',
                    'icon' => 'pending_actions',
                ],
                [
                    'label' => 'Proyectos',
                    'route' => './project',
                    'icon' => 'hub',
                ],
                [
                    'label' => 'Proyectos en curso',
                    'route' => './project-start',
                    'icon' => 'hub',
                ],
            ],
            'controllers' => [
                ProjectController::class,
                ProjectActionController::class,
                ProjectFilterController::class,
            ],
        ],
        [
            'label' => 'Notarial',
            'icon' => 'balance',
            'route' => './notary',
            'dropdowns' => [
                [
                    'label' => 'Participaciones',
                    'route' => './stakes',
                    'icon' => 'supervised_user_circle',
                ],
                [
                    'label' => 'Operaciones',
                    'route' => './operations',
                    'icon' => 'format_list_bulleted',
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
                    'label' => 'Operaciones de eliminación',
                    'route' => './disponsalOperation',
                    'icon' => 'money',
                ],
                [
                    'label' => 'Enajenación de Bienes',
                    'route' => './disponsalRealEstate',
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
            'label' => 'Documentación interna',
            'icon' => 'storage',
            'route' => './isoDocumentation',
            'controllers' => [
                IsoDocumentController::class
            ]
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
                    'controllers' => [],
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
