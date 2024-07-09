<?php
/**
 * @author open2code
 */
namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\ApiController;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Place controller first version
 */
class WarehouseController extends ApiController
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
            $response = $this->showList(Warehouse::search($request->get('search'))->orderBy('id','desc')->paginate($paginate));
        } else {
            $response = $this->showList(Warehouse::orderBy('id','desc')->paginate($paginate));
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
    public function store(Request $request): JsonResponse
    {
        $this->validate($request, Warehouse::rules());
        $warehouse = Warehouse::create($request->all());

        return $this->showOne($warehouse);
    }

    /**
     * Display the specified resource.
     *
     * @param Warehouse $warehouse
     *
     * @return JsonResponse
     */
    public function show(Warehouse $warehouse): JsonResponse
    {
        return $this->showOne($warehouse);
    }

    /**
     * @param Request $request
     * @param Warehouse $warehouse
     *
     * @return JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Warehouse $warehouse): JsonResponse
    {
        $this->validate($request, Warehouse::rules());
        $warehouse->fill($request->all());
        if ($warehouse->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $warehouse->save();

        return $this->showOne($warehouse);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Warehouse $warehouse
     *
     * @return JsonResponse
     */
    public function destroy(Warehouse $warehouse): JsonResponse
    {
        $warehouse->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
