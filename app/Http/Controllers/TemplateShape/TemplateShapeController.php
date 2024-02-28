<?php
/**
 * open2code */
namespace App\Http\Controllers\TemplateShape;

use App\Http\Controllers\ApiController;
use App\Models\TemplateShape;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * template controller first version
 */
class TemplateShapeController extends ApiController
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
            $response = $this->showList(TemplateShape::search($request->get('search')->orderBy('id','desc')->paginate($paginate)));
        } else {
            $response = $this->showList(TemplateShape::orderBy('id','desc')->paginate($paginate));
        }

        return $response;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $this->validate($request, TemplateShape::rules());

        $templateShape = new TemplateShape($request->all());

        if (!$templateShape->verifyForm()) {
            return $this->errorResponse('this form format not valid', 422);
        }

        $templateShape->save();

        return $this->showOne($templateShape);
    }

    /**
     * Display the specified resource.
     *
     * @param TemplateShape $templateShape
     * @return JsonResponse
     */
    public function show(TemplateShape $templateShape): JsonResponse
    {
        return $this->showOne($templateShape);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param TemplateShape $templateShape
     *
     * @return Response
     */
    public function update(Request $request, TemplateShape $templateShape): JsonResponse
    {
        $this->validate($request, TemplateShape::rules());
        $templateShape->fill($request->all());
        if ($templateShape->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        if (!$templateShape->verifyForm()) {
            $this->errorResponse('this form format not valid', 422);
        }

        $templateShape->save();

        return $this->showOne($templateShape);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param TemplateShape $templateShape
     *
     * @return JsonResponse
     */
    public function destroy(TemplateShape $templateShape): JsonResponse
    {
        $templateShape->delete();

        return $this->showMessage('Se elimino con éxito');
    }
}
