<?php

/*
 * CODE
 * TemplateQuotes Controller
*/

namespace App\Http\Controllers\TemplateQuotes;

use Exception;
use App\Models\TemplateQuotes;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class TemplateQuotesController extends ApiController
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
            $response = $this->showList(TemplateQuotes::search($request->get('search'))->orderBy('id','desc')->paginate($paginate));
        } else {
            $response = $this->showList(TemplateQuotes::orderBy('id','desc')->paginate($paginate));
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
        $this->validate($request, TemplateQuotes::rules());
        $templateQuotes = TemplateQuotes::create($request->all());

        return $this->showOne($templateQuotes);
    }

    /**
     * @param TemplateQuotes $templateQuote
     *
     * @return JsonResponse
     */
    public function show(TemplateQuotes $templateQuote): JsonResponse
    {
        return $this->showOne($templateQuote);
    }

    /**
     * @param Request        $request
     * @param TemplateQuotes $templateQuote
     *
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function update(Request $request, TemplateQuotes $templateQuote): JsonResponse
    {
        $this->validate($request, TemplateQuotes::rules());
        $templateQuote->fill($request->all());
        if ($templateQuote->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $templateQuote->save();

        return $this->showOne($templateQuote);
    }

    /**
     * @param TemplateQuotes $templateQuote
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(TemplateQuotes $templateQuote): JsonResponse
    {
        $templateQuote->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
