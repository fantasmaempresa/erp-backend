<?php
/**
 * @author open2code
 */
namespace App\Http\Controllers\OfficeSecurityMeasures;

use App\Http\Controllers\ApiController;
use App\Models\OfficeSecurityMeasures;
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
        $OfficeSecurityMeasuress = OfficeSecurityMeasures::create($request->all());

        return $this->showOne($OfficeSecurityMeasuress);
    }

    /**
     * Display the specified resource.
     *
     * @param OfficeSecurityMeasures $OfficeSecurityMeasuress
     *
     * @return JsonResponse
     */
    public function show(OfficeSecurityMeasures $OfficeSecurityMeasuress): JsonResponse
    {
        return $this->showOne($OfficeSecurityMeasuress);
    }

    /**
     * @param Request $request
     * @param OfficeSecurityMeasures $OfficeSecurityMeasuress
     *
     * @return JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, OfficeSecurityMeasures $OfficeSecurityMeasuress): JsonResponse
    {
        $this->validate($request, OfficeSecurityMeasures::rules());
        $OfficeSecurityMeasuress->fill($request->all());
        if ($OfficeSecurityMeasuress->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $OfficeSecurityMeasuress->save();

        return $this->showOne($OfficeSecurityMeasuress);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param OfficeSecurityMeasures $OfficeSecurityMeasuress
     *
     * @return JsonResponse
     */
    public function destroy(OfficeSecurityMeasures $OfficeSecurityMeasuress): JsonResponse
    {
        $OfficeSecurityMeasuress->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
