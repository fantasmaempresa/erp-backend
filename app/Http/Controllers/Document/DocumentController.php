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
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        if ($request->has('search')) {
            $response = $this->showList(Document::search($request->get('search'))->paginate($paginate));
        } else {
            $response = $this->showList(Document::paginate($paginate));
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
        $this->validate($request, Document::rules());
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
        $this->validate($request, Document::rules());
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
