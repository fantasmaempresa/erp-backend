<?php

/*
 * CODE
 * Role Controller
*/

namespace App\Http\Controllers\Role;

use Exception;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class RoleController extends ApiController
{

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        if ($request->has('search')) {
            $response = $this->showList(Role::search($request->get('search'))->paginate($paginate));
        } else {
            $response = $this->showList(Role::paginate($paginate));
        }

        return $response;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $this->validate($request, Role::rules());
        $role = new Role();
        $verifyConfig = $role->verifyConfig($request->get('config'));
        if ($verifyConfig) {
            return $this->errorResponse($verifyConfig, 409);
        }
        $role = Role::create($request->all());

        return $this->showOne($role);
    }

    /**
     * @param Role $role
     *
     * @return JsonResponse
     */
    public function show(Role $role): JsonResponse
    {
        return $this->showOne($role);
    }

    /**
     * @param Request $request
     * @param Role $role
     *
     * @return JsonResponse
     */
    public function update(Request $request, Role $role): JsonResponse
    {
        $this->validate($request, Role::rules());
        $role->fill($request->all());
        if ($role->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }
        $role->save();

        return $this->showOne($role);
    }

    /**
     * @param Role $role
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Role $role): JsonResponse
    {
        $role->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
