<?php

/*
 * CODE
 * Clients Controller
*/

namespace App\Http\Controllers\Clients;

use Exception;
use App\Models\Clients;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class ClientsController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $clients = Clients::all();

        return $this->showAll($clients);
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
        $rules = [];

        $this->validate($request, $rules);
        $clients = Clients::create($request->all());

        return $this->showOne($clients);
    }

    /**
     * @param Clients $clients
     *
     * @return JsonResponse
     */
    public function show(Clients $clients): JsonResponse
    {
        return $this->showOne($clients);
    }

    /**
     * @param Request $request
     * @param Clients $clients
     *
     * @return JsonResponse
     */
    public function update(Request $request, Clients $clients): JsonResponse
    {
        $clients->fill($request->all());
        if ($clients->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $clients->save();

        return $this->showOne($clients);
    }

    /**
     * @param Clients $clients
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Clients $clients): JsonResponse
    {
        $clients->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
