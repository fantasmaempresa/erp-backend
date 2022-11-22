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
use App\Http\Controllers\Project\ProjectController;
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
            'icon' => 'rule_folder',
            'dropdowns' => [
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
            'label' => 'Trámites',
            'icon' => 'description',
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
