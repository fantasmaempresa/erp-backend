<?php

namespace App\Http\Controllers\Report\Deed;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DeedProjectController extends Controller
{
    public function documentParams($data){
        return [
            "data" => $data,
            "parameters" => [],
            "jasperPath" => Storage::path('reports/deeds/DEEDS.jasper'),
            "output" => Storage::path('reports/deeds/Deeds.docx'),
            "documentType" => "docx",
        ];
    }
}