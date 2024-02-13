<?php

/*
 * CODE
 * ConceptProjectQuote Controller
*/

namespace App\Http\Controllers\ConceptProjectQuote;

use Exception;
use App\Models\ConceptProjectQuote;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class ConceptProjectQuoteController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $conceptProjectQuotes = ConceptProjectQuote::orderBy('id','desc')->all();

        return $this->showAll($conceptProjectQuotes);
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
        $conceptProjectQuote = ConceptProjectQuote::create($request->all());

        return $this->showOne($conceptProjectQuote);
    }

    /**
     * @param ConceptProjectQuote $conceptProjectQuote
     *
     * @return JsonResponse
     */
    public function show(ConceptProjectQuote $conceptProjectQuote): JsonResponse
    {
        return $this->showOne($conceptProjectQuote);
    }

    /**
     * @param Request $request
     * @param ConceptProjectQuote $conceptProjectQuote
     *
     * @return JsonResponse
     */
    public function update(Request $request, ConceptProjectQuote $conceptProjectQuote): JsonResponse
    {
        $conceptProjectQuote->fill($request->all());
        if ($conceptProjectQuote->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $conceptProjectQuote->save();

        return $this->showOne($conceptProjectQuote);
    }

    /**
     * @param ConceptProjectQuote $conceptProjectQuote
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(ConceptProjectQuote $conceptProjectQuote): JsonResponse
    {
        $conceptProjectQuote->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
