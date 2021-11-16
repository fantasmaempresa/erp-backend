<?php

/*
 * CODE
 * TaxDatum Controller
*/

namespace App\Http\Controllers\TaxDatum;

use Exception;
use App\Models\TaxDatum;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class TaxDatumController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $taxData = TaxDatum::all();

        return $this->showAll($taxData);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $rules = [
            'rfc'              => 'required',
            'curp'             => 'required',
            'regime_type'      => 'required',
            'postal_code'      => 'required',
            'street'           => 'required',
            'exterior_number'  => 'required',
            'suburb'           => 'required',
            'locality'         => 'required',
            'municipality'     => 'required',
            'country'          => 'required',
            'estate'           => 'required',
            'payment_datum_id' => 'required',
        ];

        $this->validate($request, $rules);
        $taxDatum = TaxDatum::create($request->all());

        return $this->showOne($taxDatum);
    }

    /**
     * @param TaxDatum $taxDatum
     *
     * @return JsonResponse
     */
    public function show(TaxDatum $taxDatum): JsonResponse
    {
        return $this->showOne($taxDatum);
    }

    /**
     * @param Request  $request
     * @param TaxDatum $taxDatum
     *
     * @return JsonResponse
     */
    public function update(Request $request, TaxDatum $taxDatum): JsonResponse
    {
        $taxDatum->fill($request->all());
        if ($taxDatum->isClean()) {
            return $this->errorResponse(
                'A different value must be specified to update',
                422
            );
        }

        $taxDatum->save();

        return $this->showOne($taxDatum);
    }

    /**
     * @param TaxDatum $taxDatum
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(TaxDatum $taxDatum): JsonResponse
    {
        $taxDatum->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
