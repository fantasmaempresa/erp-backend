<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BuySellController extends Controller
{
    public function getStructure(...$args){
        $project = $args[0];
        $reportTextData = json_decode(Storage::get('reports/buy_sell/BuySell.json'));

        return $reportTextData;
    }

    public function getDocument(){
        return [
            "parameters" => [],
            "jasperPath" => Storage::path('reports/buy_sell/BUY_SELL.jasper'),
            "output" => Storage::path('reports/buy_sell/BuySell.rtf'),
            "documentType" => "rtf",
        ];
    }
}
