<?php

/*
 * CODE
 * Document Controller
*/

namespace App\Http\Controllers\Document;

use Exception;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class DocumentController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $documents = Document::all();

        return $this->showAll($documents);
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
        $document = Document::create($request->all());

        return $this->showOne($document);
    }

    /**
     * @param Document $document
     *
     * @return JsonResponse
     */
    public function show(Document $document): JsonResponse
    {
        return $this->showOne($document);
    }

    /**
     * @param Request $request
     * @param Document $document
     *
     * @return JsonResponse
     */
    public function update(Request $request, Document $document): JsonResponse
    {
        $document->fill($request->all());
        if ($document->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $document->save();

        return $this->showOne($document);
    }

    /**
     * @param Document $document
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Document $document): JsonResponse
    {
        $document->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
