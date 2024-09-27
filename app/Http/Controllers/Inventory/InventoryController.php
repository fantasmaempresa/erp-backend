<?php
/**
 * @author open2code
 */
namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\ApiController;
use App\Models\Inventory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Place controller first version
 */
class InventoryController extends ApiController
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {

        $this->validate($request, [
            'warehouse_id' => 'required|int', 
            'view' => 'required|string'
        ]);


        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        if ($request->get('view') == 'inventory') {
            $warehouseId = $request->input('warehouse_id');
            $response = $this->showList(Inventory::where('warehouse_id', $warehouseId)->orderBy('id','desc')->paginate($paginate));
        } else {
            return $this->errorResponse('value view not correct', 409);
        }
        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param Inventory $inventory
     *
     * @return JsonResponse
     */
    public function show(Inventory $inventory): JsonResponse
    {
        return $this->showOne($inventory);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param Inventory $inventory
     *
     * @return JsonResponse
     */
    public function destroy(Inventory $inventory): JsonResponse
    {
        $inventory->delete();
        return $this->showMessage('Record deleted successfully');
    }
}
