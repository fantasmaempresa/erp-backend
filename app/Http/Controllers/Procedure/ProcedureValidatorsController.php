<?php

namespace App\Http\Controllers\Procedure;

use App\Http\Controllers\ApiController;
use App\Models\Procedure;
use Illuminate\Http\JsonResponse;

class ProcedureValidatorsController extends ApiController
{
    /**
     * @param string $name
     *
     * @return JsonResponse
     */
    public function uniqueValueValidator(string $name): JsonResponse
    {

        $procedure = Procedure::where('name', $name)->first();

        if ($procedure) {
            return $this->showList(false);
        }

        return $this->showList(true);
    }

    public function uniqueFolioValueValidator(string $folio): JsonResponse
    {
        $procedure_folio_min = Procedure::where('folio_min', $folio)->first();
        $procedure_folio_max = Procedure::where('folio_max', $folio)->first();

        $procedure_range_folio = Procedure::where('folio_min', '>', $folio)->where('folio_max', '<', $folio)->count();

        if ($procedure_folio_min || $procedure_folio_max || $procedure_range_folio > 0) {
            return $this->showList(false);
        }

        return $this->showList(true);
    }
}
