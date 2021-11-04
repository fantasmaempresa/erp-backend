<?php

/*
 * CODE
 * Client Controller
*/

namespace App\Http\Controllers\Client;

use Exception;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class ClientController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $clients = Client::all();

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
        $clients = Client::create($request->all());

        return $this->showOne($clients);
    }

    /**
     * @param Client $clients
     *
     * @return JsonResponse
     */
    public function show(Client $clients): JsonResponse
    {
        return $this->showOne($clients);
    }

    /**
     * @param Request $request
     * @param Client $clients
     *
     * @return JsonResponse
     */
    public function update(Request $request, Client $clients): JsonResponse
    {
        $clients->fill($request->all());
        if ($clients->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $clients->save();

        return $this->showOne($clients);
    }

    /**
     * @param Client $clients
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Client $clients): JsonResponse
    {
        $clients->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
