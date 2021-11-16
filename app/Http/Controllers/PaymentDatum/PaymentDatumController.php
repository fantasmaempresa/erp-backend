<?php

/*
 * CODE
 * PaymentDatum Controller
*/

namespace App\Http\Controllers\PaymentDatum;

use Exception;
use App\Models\PaymentDatum;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class PaymentDatumController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $paymentData = PaymentDatum::all();

        return $this->showAll($paymentData);
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
            'payment_periodicity' => 'required',
            'square'              => 'required',
        ];

        $this->validate($request, $rules);
        $paymentDatum = PaymentDatum::create($request->all());

        return $this->showOne($paymentDatum);
    }

    /**
     * @param PaymentDatum $paymentDatum
     *
     * @return JsonResponse
     */
    public function show(PaymentDatum $paymentDatum): JsonResponse
    {
        return $this->showOne($paymentDatum);
    }

    /**
     * @param Request      $request
     * @param PaymentDatum $paymentDatum
     *
     * @return JsonResponse
     */
    public function update(Request $request, PaymentDatum $paymentDatum): JsonResponse
    {
        $paymentDatum->fill($request->all());
        if ($paymentDatum->isClean()) {
            return $this->errorResponse(
                'A different value must be specified to update',
                422
            );
        }

        $paymentDatum->save();

        return $this->showOne($paymentDatum);
    }

    /**
     * @param PaymentDatum $paymentDatum
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(PaymentDatum $paymentDatum): JsonResponse
    {
        $paymentDatum->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
