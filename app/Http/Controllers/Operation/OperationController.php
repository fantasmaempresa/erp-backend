<?php
/**
 * open2code first version
 */

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\ApiController;
use App\Models\Operation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Js;

/**
 * Operation Controller open2code
 */
class OperationController extends ApiController
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
            $response = $this->showList(Operation::search($request->get('search'))->with('categoryOperation')->orderBy('name')->paginate($paginate));
        } else {
            $response = $this->showList(Operation::with('categoryOperation')->orderBy('name')->paginate($paginate));
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
        $this->validate($request, Operation::rules());
        $operation = new Operation($request->all());
        $operation->config = 
        array_merge(empty($operation->config) ? [] : $operation->config, 
            ['documents_required' => $request->get('documents')]
        );
        $operation->save();
        return $this->showOne($operation);

    }

    /**
     * Display the specified resource.
     *
     * @param Operation $operation
     *
     * @return JsonResponse
     */
    public function show(Operation $operation): JsonResponse
    {
        $operation->categoryOperation;
        return $this->showOne($operation);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Operation $operation
     *
     * @return JsonResponse
     */
    public function update(Request $request, Operation $operation): JsonResponse
    {
        $this->validate($request, Operation::rules());
        $operation->fill($request->all());
        if ($operation->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $operation->save();

        return $this->showOne($operation);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Operation $operation
     *
     * @return JsonResponse
     */
    public function destroy(Operation $operation): JsonResponse
    {
        $operation->delete();

        return $this->showMessage('Se elimino con éxito');
    }
}
