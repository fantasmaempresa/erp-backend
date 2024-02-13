<?php

/*
 * CODE
 * ClientDocument Controller
*/

namespace App\Http\Controllers\ClientDocument;

use Exception;
use App\Models\ClientDocument;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class ClientDocumentController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $clientDocuments = ClientDocument::orderBy('id','desc')->all();

        return $this->showAll($clientDocuments);
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
        $clientDocument = ClientDocument::create($request->all());

        return $this->showOne($clientDocument);
    }

    /**
     * @param ClientDocument $clientDocument
     *
     * @return JsonResponse
     */
    public function show(ClientDocument $clientDocument): JsonResponse
    {
        return $this->showOne($clientDocument);
    }

    /**
     * @param Request $request
     * @param ClientDocument $clientDocument
     *
     * @return JsonResponse
     */
    public function update(Request $request, ClientDocument $clientDocument): JsonResponse
    {
        $clientDocument->fill($request->all());
        if ($clientDocument->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $clientDocument->save();

        return $this->showOne($clientDocument);
    }

    /**
     * @param ClientDocument $clientDocument
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(ClientDocument $clientDocument): JsonResponse
    {
        $clientDocument->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
