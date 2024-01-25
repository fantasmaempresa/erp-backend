<?php

/*
 * OPEN2CODE DisposalRealEstateController
 */

namespace App\Http\Controllers\DisposalRealEstate;

use App\Http\Controllers\ApiController;
use App\Models\DisposalRealEstate;
use App\Models\Rate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

/**
 * version
 */
class DisposalRealEstateController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        return $this->showAll(DisposalRealEstate::paginate($paginate));
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

        //CCA
        $landProportion = $disposalRealEstate->land_proportion / 100;
        $constructionProportion = isset($disposalRealEstate->construction_proportion) ? $disposalRealEstate->construction_proportion / 100 : 0;
        $depreciationRate = $disposalRealEstate->depreciation_rate / 100;

        $disposalRealEstate->acquisition_value_transferor = $disposalRealEstate->acquisition_value / $acquirers;
        $disposalRealEstate->value_land_proportion = $disposalRealEstate->acquisition_value_transferor * $landProportion;
        $disposalRealEstate->value_construction_proportion = $disposalRealEstate->acquisition_value_transferor * $constructionProportion;
        $disposalRealEstate->annual_depreciation = $depreciationRate * $disposalRealEstate->value_construction_proportion;

        $disposalDate = new \DateTime($disposalRealEstate->disposal_date);
        $acquisitionDate = new \DateTime($disposalRealEstate->acquisition_date);
        $differenceDate = $disposalDate->diff($acquisitionDate);
        $disposalRealEstate->years_passed = $differenceDate->y;

        $disposalRealEstate->depreciation_value = $disposalRealEstate->annual_depreciation * $disposalRealEstate->years_passed;
        $disposalRealEstate->construction_value = $disposalRealEstate->value_construction_proportion - $disposalRealEstate->depreciation_value;

        $npciDisposal = DisposalRealEstate::getNPCI(
            (int)$disposalDate->format('m') - 1,
            (int)$disposalDate->format('Y')
        );
        $npciAcquisition = DisposalRealEstate::getNPCI(
            (int)$acquisitionDate->format('m'),
            (int)$acquisitionDate->format('Y')
        );

        $disposalRealEstate->ncpi_disposal_id = $npciDisposal->id;
        $disposalRealEstate->ncpi_acquisition_id = $npciAcquisition->id;
        $disposalRealEstate->annex_factor = round(($npciDisposal->value / $npciAcquisition->value), 4);

        $disposalRealEstate->updated_construction_cost = $disposalRealEstate->construction_value * $disposalRealEstate->annex_factor;
        $disposalRealEstate->updated_land_cost = $disposalRealEstate->acquisition_value_transferor * $disposalRealEstate->annex_factor;
        $disposalRealEstate->updated_total_cost_acquisition = $disposalRealEstate->updated_land_cost + $disposalRealEstate->updated_construction_cost;

        //ISR DISPOSAL
        $disposalRealEstate->disposal_value_transferor = $disposalRealEstate->disposal_value / $acquirers;
        $disposalRealEstate->preventive_notices = $disposalRealEstate->improvements + $disposalRealEstate->appraisal + $disposalRealEstate->commissions + $disposalRealEstate->isabi;
        $disposalRealEstate->tax_base = $disposalRealEstate->disposal_value_transferor
            - $disposalRealEstate->updated_total_cost_acquisition
            - $disposalRealEstate->preventive_notices;
        $disposalRealEstate->cumulative_profit = $disposalRealEstate->tax_base / $disposalRealEstate->years_passed;
        $disposalRealEstate->not_cumulative_profit = $disposalRealEstate->tax_base - $disposalRealEstate->cumulative_profit;

        $rate = Rate::where('year', $disposalDate->format('Y'))
            ->where('lower_limit', '<=', $disposalRealEstate->cumulative_profit)
            ->where('upper_limit', '>=', $disposalRealEstate->cumulative_profit)
            ->first();

        $disposalRealEstate->rate_id = $rate->id;
        $disposalRealEstate->surplus = $disposalRealEstate->cumulative_profit - $rate->lower_limit;
        $disposalRealEstate->marginal_tax = $disposalRealEstate->surplus * ($rate->surplus / 100);
        $disposalRealEstate->isr_charge = $disposalRealEstate->marginal_tax + $rate->fixed_fee;
        $disposalRealEstate->isr_pay = $disposalRealEstate->isr_charge * $disposalRealEstate->years_passed;
        $disposalRealEstate->isr_federal_entity_pay = $disposalRealEstate->isr_pay;
        $disposalRealEstate->taxable_gain = $disposalRealEstate->tax_base;
        $disposalRealEstate->isr_federal_entity = $disposalRealEstate->taxable_gain * ($disposalRealEstate->rate / 100);
        $disposalRealEstate->isr_federation = $disposalRealEstate->isr_federal_entity_pay - $disposalRealEstate->isr_federal_entity;

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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\DisposalRealEstate $disposalRealEstate
     * @return Response
     */
    public function destroy(DisposalRealEstate $disposalRealEstate)
    {
        //
    }
}
