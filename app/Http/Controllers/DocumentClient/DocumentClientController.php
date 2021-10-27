<?php

/*
 * CODE
 * DocumentClient Controller
*/

namespace App\Http\Controllers\DocumentClient;

use Exception;
use App\Models\DocumentClient;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class DocumentClientController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $documentClients = DocumentClient::all();

        return $this->showAll($documentClients);
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
        $documentClient = DocumentClient::create($request->all());

        return $this->showOne($documentClient);
    }

    /**
     * @param DocumentClient $documentClient
     *
     * @return JsonResponse
     */
    public function show(DocumentClient $documentClient): JsonResponse
    {
        return $this->showOne($documentClient);
    }

    /**
     * @param Request $request
     * @param DocumentClient $documentClient
     *
     * @return JsonResponse
     */
    public function update(Request $request, DocumentClient $documentClient): JsonResponse
    {
        $documentClient->fill($request->all());
        if ($documentClient->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $documentClient->save();

        return $this->showOne($documentClient);
    }

    /**
     * @param DocumentClient $documentClient
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(DocumentClient $documentClient): JsonResponse
    {
        $documentClient->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
