<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\ApiController;
use App\Models\Inventory;
use App\Models\Article;
use App\Models\OfficeSecurityMeasures;
use Illuminate\Http\Request;

class InventoryActionController extends ApiController
{
    public function addArticleToInventory(Request $request){
        $this->validate($request, Inventory::rules(id: null));
        $inventoryEntry = Inventory::where('warehouse_id', $request->input('warehouse_id'))
        ->where('article_id', $request->input('article_id'))
        ->first();

        if(!$inventoryEntry){
            $this->newInventoryEntry($request->input('article_id') , $request->input('warehouse_id') , $request->input('amount'));
        } else {
            $inventoryEntry->amount += $request->input('amount');
            $inventoryEntry->save();
        }

        return $this->showMessage('Article added successfully');
    }

    //Pendiente de creacion del funcionamiento de la operacion
    public function removeArticleFromInventory(Request $request){
        $inventoryEntry = Inventory::where('id', $request->input('id'))
        ->first();

        if(!$inventoryEntry){
            return $this->showMessage('Article removed successfully');
        } else {
            $inventoryEntry->amount += $request->input('amount');
            $inventoryEntry->save();
        }
        
        return $this->showMessage('Article removed successfully');
    }

    public function inventoryWarehouseItemTransfer(Request $request){
        // Request Validation
        $this->validate($request,[
            "article_id"=>"required|exists:articles,id",
            "origin_warehouse_id"=>"required|exists:warehouses,id",
            "destiny_warehouse_id"=>"required|exists:warehouses,id",
            "amount"=>"required|int"
        ]);

        // Necessary data retrival
        $originWarehouseInventoryEntry = Inventory::where('warehouse_id', $request->input('origin_warehouse_id'))
        ->where('article_id', $request->input('article_id'))
        ->first();
        $destinyWarehouseInventoryEntry = Inventory::where('warehouse_id', $request->input('destiny_warehouse_id'))
        ->where('article_id', $request->input('article_id'))
        ->first();
        $amount = $request->input('amount');

        // Necessary data validation
        if (!$originWarehouseInventoryEntry) {
            return $this->errorResponse(['Origin warehouse not found or article not found in origin warehouse'], 422);
        } else if($originWarehouseInventoryEntry->amount < $amount) {
            return $this->errorResponse(['Not enough inventory to tranfer'], 422);
        }

        // Warehouse item transfer operation-----------------------------------
        $originWarehouseInventoryEntry->amount -= $amount;
        if ($originWarehouseInventoryEntry->amount <= 0) {
            $originWarehouseInventoryEntry->delete();
        } else {
            $originWarehouseInventoryEntry->save();
        }

        if (!$destinyWarehouseInventoryEntry) {// In case the destiny warehouse doesnt have the article already stored
            $this->newInventoryEntry($request->input('article_id') , $request->input('destiny_warehouse_id') , $amount);
        }
        else {// If the article is already stored in the warehouse
            $destinyWarehouseInventoryEntry->amount += $amount;
            $destinyWarehouseInventoryEntry->save();
        }       

        return $this->showMessage('Item warehouse transfer successfull');
    }

    public function warehouseSale(){
        
    }

    private function newInventoryEntry($article_id , $warehouse_id , $amount){
        $newWarehouseArticle = new Inventory();
        $newWarehouseArticle->article_id = $article_id;
        $newWarehouseArticle->warehouse_id = $warehouse_id;
        $newWarehouseArticle->amount = $amount;
        $newWarehouseArticle->save();
    }
}
