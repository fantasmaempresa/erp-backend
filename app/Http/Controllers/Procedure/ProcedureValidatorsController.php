<?php

namespace App\Http\Controllers\Procedure;

use App\Http\Controllers\ApiController;
use App\Models\Procedure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class ProcedureValidatorsController extends ApiController
{
    /**
     * @param string $name
     *
     * @return JsonResponse
     */
    public function uniqueValueValidator(string $name, Request $request): JsonResponse
    {
        $this->validate($request, [
            'id' => 'nullable|int',
        ]);

        $id = $request->get('id') ?? null;

        if (is_null($id)) {
            $procedure = Procedure::where('name',  $name)->first();
        } else {
            $procedure = Procedure::where('name', $name)->where('id', '<>', $id)->first();
        }

        if (is_null($procedure)) {
            return $this->showList(true);
        } else {
            return $this->showList(false);
        }
    }

     /**
     * @param string $name
     *
     * @return JsonResponse
     */
    public function uniqueValueInstrumentValidator(string $name, Request $request): JsonResponse
    {
        $this->validate($request, [
            'id' => 'nullable|int',
        ]);

        $id = $request->get('id') ?? null;

        if (is_null($id)) {
            $procedure = Procedure::where('instrument',  $name)->first();
        } else {
            $procedure = Procedure::where('instrument', $name)->where('id', '<>', $id)->first();
        }

        if (is_null($procedure)) {
            return $this->showList(true);
        } else {
            return $this->showList(false);
        }
    }

    public function uniqueFolioValueValidator(string $folio, Request $request): JsonResponse
    {
        $this->validate($request, [
            'id' => 'nullable|int',
            'range' => 'required|string'
        ]);

        $id = $request->get('id') ?? null;
        $procedure_folio = null;

        if (is_null($id)) {
            $procedure_folio = Procedure::where($request->get('range'), $folio)->first();
        } else {
            $procedure_folio = Procedure::where($request->get('range'), $folio)->where('id', '<>', $id)->first();
        }

        $procedure_range_folio = Procedure::where('folio_min', '>', $folio)->where('folio_max', '<', $folio)->count();

        if (is_null($procedure_folio) && $procedure_range_folio == 0) {
            return $this->showList(true);
        } else {
            return $this->showList(false);
        }
    }
}
