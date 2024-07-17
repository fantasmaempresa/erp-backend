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
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        if (!empty($request->get('search')) && $request->get('search') !== 'null') {
            $response = $this->showList(Inventory::search($request->get('search'))->orderBy('id','desc')->paginate($paginate));
        } else {
            $response = $this->showList(Inventory::orderBy('id','desc')->paginate($paginate));
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
