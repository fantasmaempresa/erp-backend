<?php

namespace App\Http\Controllers\Shape;

use App\Http\Controllers\ApiController;
use App\Models\Shape;
use Illuminate\Http\Request;

class ShapeValidationController extends ApiController
{
     /**
     * @param string $name
     *
     * @return JsonResponse
     */
    public function uniqueValueValidator(string $name): JsonResponse
    {

        $procedure = Shape::where('name', $name)->first();

        if ($procedure) {
            return $this->showList(false);
        }

        return $this->showList(true);
    }
    
}
