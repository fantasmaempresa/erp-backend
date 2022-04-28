<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Role\RoleActionController;
use App\Models\Role;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class Permission
{
    use ApiResponseTrait;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $methodsView = ['POST', 'PUT', 'PATCH', 'DELETE'];

        $user = User::findOrFail(Auth::id());
        if ($user->role_id === Role::$ADMIN) {
            return $next($request);
        }

        if ($user->role->config['view_mode'] && in_array($request->getMethod(), $methodsView)) {
            return $this->errorResponse('you do not have permissions to enter this route', 401);
        }

        $controller = Route::currentRouteAction();
        $controller = substr($controller, 0, strpos($controller, '@'));

        foreach ($user->role->config['modules'] as $module) {
            foreach (RoleActionController::$startMenu as $menu) {
                if ($module['name'] === $menu['label']) {
                    foreach ($menu['controllers'] as $controllerMenu) {
                        if ($controller === $controllerMenu) {
                            return $next($request);
                        }
                    }
                }
            }
        }

        return $this->errorResponse('you do not have permissions to enter this route', 401);
    }
}
