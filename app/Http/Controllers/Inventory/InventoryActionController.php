<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\ApiController;
use App\Models\Inventory;
use App\Models\Article;
use App\Models\MovementTracking;

use Illuminate\Http\Request;

class InventoryActionController extends ApiController
{
    public function initialInventory(Request $request){
        $this->validate($request,[
            "article_id"=>"required|exists:articles,id",
            "warehouse_id"=>"required|exists:warehouses,id"
        ]);
        $inventoryEntry = Inventory::where('warehouse_id', $request->input('warehouse_id'))
            ->where('article_id', $request->input('article_id'))
            ->first();
        if($inventoryEntry){
            return $this->errorResponse(['The article is already in the warehouse inventory'], 422);
        } else{
            $article=Article::where('id', $request->input('article_id'))->first();
            if($article){
                $this->newInventoryEntry($request->input('article_id') , $request->input('warehouse_id') ,0);
                $this->newMovementTrackingEntry($request->input('article_id'), $request->input('warehouse_id'),0 , "Initial Inventory");
            } else{
                return $this->errorResponse(['The article does not exists'], 422);
            }
        }
        return $this->showMessage('Article added successfully');
    }

    public function purchase(Request $request){
        $this->validate($request, Inventory::rules(id: null));
        $inventoryEntry = Inventory::where('warehouse_id', $request->input('warehouse_id'))
            ->where('article_id', $request->input('article_id'))
            ->first();
        if($inventoryEntry){
            $inventoryEntry->amount += $request->input('amount');
            $inventoryEntry->save();
            $this->newMovementTrackingEntry($request->input('article_id'), $request->input('warehouse_id'), $request->input('amount'), "Purchase");
        }else{
            return $this->errorResponse(['The warehouse does not contain an inventory for this article'], 422);
        }
        return $this->showMessage('Article purchase was successfull');
    }

    public function sale(Request $request){
        $this->validate($request, Inventory::rules(id: null));
        $inventoryEntry = Inventory::where('warehouse_id', $request->input('warehouse_id'))
            ->where('article_id', $request->input('article_id'))
            ->first();

        if(!$inventoryEntry){
            return $this->errorResponse(['The article does not exist in the warehouse inventory'], 422);
        } else if($inventoryEntry->amount < $request->input('amount')) {
            return $this->errorResponse(['Not enough inventory to sale'], 422);
        } else {
            $inventoryEntry->amount -= $request->input('amount');
            $inventoryEntry->save();
            $this->newMovementTrackingEntry($request->input('article_id'), $request->input('warehouse_id'), $request->input('amount'), "Sale");
        }
        return $this->showMessage('Article sale was successfull');
    }

    public function warehouseTransfer(Request $request){
        // Request Validation
        $this->validate($request,[
            "article_id"=>"required|exists:articles,id",
            "origin_warehouse_id"=>"required|exists:warehouses,id",
            "destiny_warehouse_id"=>"required|exists:warehouses,id",
            "amount"=>"required|int"
        ]);

        $originWarehouseInventoryEntry = Inventory::where('warehouse_id', $request->input('origin_warehouse_id'))
            ->where('article_id', $request->input('article_id'))
            ->first();
        $destinyWarehouseInventoryEntry = Inventory::where('warehouse_id', $request->input('destiny_warehouse_id'))
            ->where('article_id', $request->input('article_id'))
            ->first();
        $amount = $request->input('amount');

        if (!$originWarehouseInventoryEntry) {
            return $this->errorResponse(['Origin warehouse not found or article not found in origin warehouse'], 422);
        } else if($originWarehouseInventoryEntry->amount < $amount) {
            return $this->errorResponse(['Not enough article in inventory to tranfer'], 422);
        }else if (!$destinyWarehouseInventoryEntry) {
            return $this->errorResponse(['Destiny warehouse does not contain an inventory for this article'], 422);
        }

        // Warehouse item transfer operation-----------------------------------
        $originWarehouseInventoryEntry->amount -= $amount;
        $originWarehouseInventoryEntry->save();
        $destinyWarehouseInventoryEntry->amount += $amount;
        $destinyWarehouseInventoryEntry->save();   
        
        $this->newMovementTrackingEntry($request->input('article_id'), $request->input('origin_warehouse_id'), $request->input('amount'), "Warehouse Transfer");

        return $this->showMessage('Item warehouse transfer successfull');
    }

    private function newInventoryEntry($article_id , $warehouse_id , $amount){
        $newWarehouseArticle = new Inventory();
        $newWarehouseArticle->article_id = $article_id;
        $newWarehouseArticle->warehouse_id = $warehouse_id;
        $newWarehouseArticle->amount = $amount;
        $newWarehouseArticle->save();
    }

    private function newMovementTrackingEntry($article_id, $warehouse_id, $amount, $reason){
        $newMovementTrackingEntry = new MovementTracking();
        $newMovementTrackingEntry->article_id = $article_id;
        $newMovementTrackingEntry->warehouse_id = $warehouse_id;
        $newMovementTrackingEntry->amount = $amount;
        $newMovementTrackingEntry->reason = $reason;
        $newMovementTrackingEntry->save();
    }


}
