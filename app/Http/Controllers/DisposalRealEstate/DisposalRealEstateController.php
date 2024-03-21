<?php

/*
 * OPEN2CODE DisposalRealEstateController
 */

namespace App\Http\Controllers\DisposalRealEstate;

use App\Http\Controllers\ApiController;
use App\Models\DisposalRealEstate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * version
 */
class DisposalRealEstateController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $paginate = $request->get('paginate', env('NUMBER_PAGINATE'));

        $query = DisposalRealEstate::with([
            'nationalConsumerPriceIndexDisposal',
            'nationalConsumerPriceIndexAcquisition',
            'alienating',
            'typeDisposalOperation',
            'rates',
            'acquirers',
            'appendant'
        ])
            ->orderBy('id', 'desc');

        if (!empty($request->get('search')) && $request->get('search') !== 'null') {
            $query->search($request->get('search'));
        }

        $response = $query->paginate($paginate);

        return $this->showList($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $this->validate($request, DisposalRealEstate::rules());
        $disposalRealEstate = new DisposalRealEstate($request->all());

        $acquirers = count($request->get('acquirers'));

        $disposalRealEstate->calculations($acquirers);

        DB::beginTransaction();
        try {
            $disposalRealEstate->save();

            foreach ($request->get('acquirers') as $aquire) {
                $disposalRealEstate->acquirerCalcultation($aquire);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse('something went wrong', 400);
        }

        return $this->showOne($disposalRealEstate);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\DisposalRealEstate $disposalRealEstate
     * @return Response
     */
    public function show(DisposalRealEstate $disposalRealEstate)
    {
        $disposalRealEstate->nationalConsumerPriceIndexDisposal;
        $disposalRealEstate->nationalConsumerPriceIndexAcquisition;
        $disposalRealEstate->alienating;
        $disposalRealEstate->typeDisposalOperation;
        $disposalRealEstate->rates;
        $disposalRealEstate->acquirers;
        $disposalRealEstate->appendant;

        return $this->showOne($disposalRealEstate);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param \App\Models\DisposalRealEstate $disposalRealEstate
     * @return Response
     */
    public function update(Request $request, DisposalRealEstate $disposalRealEstate)
    {
        $this->validate($request, DisposalRealEstate::rules());
        $disposalRealEstate->fill($request->all());

        $acquirers = count($request->get('acquirers'));
        $disposalRealEstate->calculations($acquirers);

        DB::beginTransaction();
        try {
            $disposalRealEstate->acquirers()->detach();
            $disposalRealEstate->save();

            foreach ($request->get('acquirers') as $aquire) {
                $disposalRealEstate->acquirerCalcultation($aquire);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse('something went wrong', 400);
        }

        return $this->showOne($disposalRealEstate);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\DisposalRealEstate $disposalRealEstate
     * @return Response
     */
    public function destroy(DisposalRealEstate $disposalRealEstate)
    {
        $disposalRealEstate->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
