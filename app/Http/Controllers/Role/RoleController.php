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
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->showList(Role::paginate(env('NUMBER_PAGINATE')));
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
        $rules = [
            'name' => 'string',
            'description' => 'string',
//            'config' => 'string',
        ];

        $this->validate($request, $rules);
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
     * @param Role    $role
     *
     * @return JsonResponse
     */
    public function update(Request $request, Role $role): JsonResponse
    {
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
