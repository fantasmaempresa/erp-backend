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
            $response = $this->showList(TemplateQuotes::search($request->get('search'))->paginate($paginate));
        } else {
            $response = $this->showList(TemplateQuotes::paginate($paginate));
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
     * @param TemplateQuotes $templateQuotes
     *
     * @return JsonResponse
     */
    public function show(TemplateQuotes $templateQuotes): JsonResponse
    {
        return $this->showOne($templateQuotes);
    }

    /**
     * @param Request        $request
     * @param TemplateQuotes $templateQuotes
     *
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function update(Request $request, TemplateQuotes $templateQuotes): JsonResponse
    {
        $this->validate($request, TemplateQuotes::rules());
        $templateQuotes->fill($request->all());
        if ($templateQuotes->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $templateQuotes->save();

        return $this->showOne($templateQuotes);
    }

    /**
     * @param TemplateQuotes $templateQuotes
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(TemplateQuotes $templateQuotes): JsonResponse
    {
        $templateQuotes->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
