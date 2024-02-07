<?php

/*
 * CODE
 * Concept Controller
*/

namespace App\Http\Controllers\Concept;

use Exception;
use App\Models\Concept;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class ConceptController extends ApiController
{

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        if (!empty($request->get('search')) && $request->get('search') !== 'null') {
            $response = $this->showList(Concept::search($request->get('search'))->paginate($paginate));
        } else {
            $response = $this->showList(Concept::paginate($paginate));
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
        $concept = new Concept();
        $this->validate($request, Concept::rules());
        $verifyFormula = $concept->verifyFormula($request->all()["formula"]);
        if ($verifyFormula) {
            return $this->errorResponse($verifyFormula, 409);
        }
        $concept = Concept::create($request->all());

        return $this->showOne($concept);
    }

    /**
     * @param Concept $concept
     *
     * @return JsonResponse
     */
    public function show(Concept $concept): JsonResponse
    {
        return $this->showOne($concept);
    }

    /**
     * @param Request $request
     * @param Concept $concept
     *
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function update(Request $request, Concept $concept): JsonResponse
    {
        $this->validate($request, Concept::rules());
        $verifyFormula = $concept->verifyFormula($request->all()["formula"]);
        if ($verifyFormula) {
            return $this->errorResponse($verifyFormula, 409);
        }
        $concept->fill($request->all());
        if ($concept->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $concept->save();

        return $this->showOne($concept);
    }

    /**
     * @param Concept $concept
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Concept $concept): JsonResponse
    {
        $concept->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
