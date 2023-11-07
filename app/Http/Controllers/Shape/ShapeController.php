<?php

namespace App\Http\Controllers\Shape;

use App\Http\Controllers\ApiController;
use App\Models\Procedure;
use App\Models\Shape;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Validation\ValidationException;

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
            $response = $this->showList(shape::search($request->get('search')->paginate($paginate)));
        }
        if ($request->has('procedure_id')) {
            $procedure = Procedure::findOrFail($request->get('procedure_id'));
            $shapes = $procedure->shapes;
            $currentPage = Paginator::resolveCurrentPage('page');
            $response = $this->showList(new LengthAwarePaginator(
                $shapes->forPage($currentPage, $paginate),
                $shapes->count(),
                $paginate,
                $currentPage,
                ['path' => Paginator::resolveCurrentPath()]
            ));
        } else {
            $response = $this->showList(shape::paginate($paginate));
        }

        return $response;
    }


    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, Shape::rules());

        $shape = new Shape($request->all());

        //TODO verificar que el fomulario que viene sea el mismo que el de la plantilla
//        if (!$shape->verifyForm()) {
//            return $this->errorResponse('this form format not valid', 422);
//        }

        $shape->signature_date = Carbon::parse($shape->signature_date);
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
     * @param Shape   $shape
     *
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function update(Request $request, Shape $shape): JsonResponse
    {
        $this->validate($request, Shape::rules());
        $shape->fill($request->all());

        if ($shape->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
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
