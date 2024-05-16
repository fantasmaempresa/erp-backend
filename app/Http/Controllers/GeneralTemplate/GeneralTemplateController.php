<?php

namespace App\Http\Controllers\GeneralTemplate;

use App\Http\Controllers\ApiController;
use App\Models\GeneralTemplate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


class GeneralTemplateController extends ApiController
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
            $response = $this->showList(GeneralTemplate::search($request->get('search'))->orderBy('id','desc')->paginate($paginate));
        } else {
            $response = $this->showList(GeneralTemplate::orderBy('id','desc')->paginate($paginate));
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
        $this->validate($request, GeneralTemplate::rules());
        $GeneralTemplate = GeneralTemplate::create($request->all());

        return $this->showOne($GeneralTemplate);
    }

    /**
     * @param GeneralTemplate $templateQuote
     *
     * @return JsonResponse
     */
    public function show(GeneralTemplate $templateQuote): JsonResponse
    {
        return $this->showOne($templateQuote);
    }

    /**
     * @param Request        $request
     * @param GeneralTemplate $templateQuote
     *
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function update(Request $request, GeneralTemplate $templateQuote): JsonResponse
    {
        $this->validate($request, GeneralTemplate::rules());
        $templateQuote->fill($request->all());
        if ($templateQuote->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $templateQuote->save();

        return $this->showOne($templateQuote);
    }

    /**
     * @param GeneralTemplate $templateQuote
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(GeneralTemplate $templateQuote): JsonResponse
    {
        $templateQuote->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
