<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\ApiController;
use App\Models\Staff;
use App\Models\Article;
use App\Models\Warehouse;
use App\Models\OfficeSecurityMeasures;
use App\Models\MovementTracking;

use Illuminate\Http\Request;

class OfficeSecurityMeasuresFilterController extends ApiController
{
    public function getStaffOfficeSecurityMeasures(Request $request){
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');
        
        $request->validate([
            'staff_id' => 'required|integer',
        ]);

        $staffId = $request->input('staff_id');

        $response = OfficeSecurityMeasures::where('staff_id', $staffId)->orderBy('id','desc')->paginate($paginate);

        if ($response->isEmpty()) {
            return $this->errorResponse(['The staff does not have Office Security Measures'], 422);
        }

        return $response;
    }
}