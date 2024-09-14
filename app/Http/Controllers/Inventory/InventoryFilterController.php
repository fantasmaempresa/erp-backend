<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\ApiController;
use App\Models\Inventory;
use App\Models\Article;
use App\Models\OfficeSecurityMeasures;

use Illuminate\Http\Request;

class InventoryFilterController extends ApiController
{
    public function getWarehouseInventory(Request $request){

        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');
        
        $request->validate([
            'warehouse_id' => 'required|integer',
        ]);

        $warehouseId = $request->input('warehouse_id');

        $response = Inventory::where('warehouse_id', $warehouseId)->orderBy('id','desc')->paginate($paginate);

        if ($response->isEmpty()) {
            return $this->errorResponse(['The warehouse is empty'], 422);
        }

        return $response;
    }
}
