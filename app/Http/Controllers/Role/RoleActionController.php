<?php
/*
 * CODE
 * Role Action Controller
*/

namespace App\Http\Controllers\Role;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Concept\ConceptController;
use App\Http\Controllers\PhasesProcess\PhasesProcessController;
use App\Http\Controllers\Project\ProjectActionController;
use App\Http\Controllers\Project\ProjectController;
use App\Http\Controllers\Project\ProjectFilterController;
use App\Http\Controllers\ProjectQuote\ProjectQuoteController;
use App\Http\Controllers\ProjectQuote\ProjectQuoteFilterController;
use App\Http\Controllers\ProjectQuote\ProjectQuoteOperationsController;
use App\Http\Controllers\Staff\StaffController;
use App\Http\Controllers\TemplateQuotes\TemplateQuotesController;
use App\Http\Controllers\User\UserController;
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
    public static $startMenu = [
        [
            'label' => 'Clientes',
            'route' => './clients',
            'icon' => 'people',
            'controllers' => [
                ClientController::class,
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
                    'label' => 'Comenzar Proyecto',
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
            'label' => 'Trámites',
            'icon' => 'description',
            'route' => './procedures',
            'dropdowns' => [
                [
                    'label' => 'Pendientes',
                    'route' => './pending_procedures',
                    'icon' => 'pause',
                ],
                [
                    'label' => 'En Curso',
                    'route' => './ongoing_procedure',
                    'icon' => 'play_arrow',
                ],
            ],
            'controllers' => [
                PhasesProcessController::class,
                ProjectController::class,
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
            'submenus' => [],
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
