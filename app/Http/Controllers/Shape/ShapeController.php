<?php

namespace App\Http\Controllers\Shape;

use App\Http\Controllers\ApiController;
use App\Models\Shape;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShapeController extends ApiController
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
            $response = $this->showList(Shape::search($request->get('search')->paginate($paginate)));
        } else {
            $response = $this->showList(Shape::paginate($paginate));
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
    public function store(Request $request)
    {
        $this->validate($request, Shape::rules());

        $shape = new Shape($request->all());

        if (!$shape->verifyForm()) {
            return $this->errorResponse('this form format not valid', 422);
        }

        $shape->save();

        return $this->showOne($shape);
    }

    /**
     * Display the specified resource.
     *
     * @param Shape $shape
     *
     * @return JsonResponse
     */
    public function show(Shape $shape)
    {
        return $this->showOne($shape);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Shape $shape
     *
     * @return JsonResponse
     */
    public function update(Request $request, Shape $shape)
    {
        $this->validate($request, Shape::rules());
        $shape->fill($request->all());
        if ($shape->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        if (!$shape->verifyForm()) {
            $this->errorResponse('this form format not valid', 422);
        }

        $shape->save();

        return $this->showOne($shape);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Shape $shape
     *
     * @return JsonResponse
     */
    public function destroy(Shape $shape): JsonResponse
    {

        //TODO agregar función para que se desvincule de los procesos que tiene
        $shape->delete();

        return $this->showMessage('Se elimino con éxito');
    }
}
