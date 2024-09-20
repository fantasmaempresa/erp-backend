<?php
/**
 * @author open2code
 */
namespace App\Http\Controllers\OfficeSecurityMeasures;

use App\Http\Controllers\ApiController;
use App\Models\OfficeSecurityMeasures;
use App\Models\MovementTracking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Place controller first version
 */
class OfficeSecurityMeasuresController extends ApiController
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        if (!empty($request->get('search')) && $request->get('search') !== 'null') {
            $response = $this->showList(OfficeSecurityMeasures::search($request->get('search'))->orderBy('id','desc')->paginate($paginate));
        } else {
            $response = $this->showList(OfficeSecurityMeasures::orderBy('id','desc')->paginate($paginate));
        }

        return $response;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $this->validate($request, OfficeSecurityMeasures::rules());
        $OfficeSecurityMeasures = OfficeSecurityMeasures::create($request->all());
        $this->newMovementTrackingEntry($request->input('article_id'), $request->input('warehouse_id'),0 , "New Office Security Measure");
        return $this->showOne($OfficeSecurityMeasures);
    }

    /**
     * Display the specified resource.
     *
     * @param OfficeSecurityMeasures $OfficeSecurityMeasures
     *
     * @return JsonResponse
     */
    public function show(OfficeSecurityMeasures $OfficeSecurityMeasures): JsonResponse
    {
        return $this->showOne($OfficeSecurityMeasures);
    }

    /**
     * @param Request $request
     * @param OfficeSecurityMeasures $OfficeSecurityMeasures
     *
     * @return JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, OfficeSecurityMeasures $OfficeSecurityMeasures): JsonResponse
    {
        $this->validate($request, OfficeSecurityMeasures::rules());
        $OfficeSecurityMeasures->fill($request->all());
        if ($OfficeSecurityMeasures->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $OfficeSecurityMeasures->save();

        return $this->showOne($OfficeSecurityMeasures);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param OfficeSecurityMeasures $OfficeSecurityMeasures
     *
     * @return JsonResponse
     */
    public function destroy(OfficeSecurityMeasures $OfficeSecurityMeasures): JsonResponse
    {
        $OfficeSecurityMeasures->delete();

        return $this->showMessage('Record deleted successfully');
    }

    private function newMovementTrackingEntry($article_id, $warehouse_id, $amount, $reason){
        $newMovementTrackingEntry = new MovementTracking();
        $newMovementTrackingEntry->article_id = $article_id;
        $newMovementTrackingEntry->warehouse_id = $warehouse_id;
        $newMovementTrackingEntry->amount = $amount;
        $newMovementTrackingEntry->reason = $reason;
        $newMovementTrackingEntry->save();
    }
}
