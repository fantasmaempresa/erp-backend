<?php
/**
 * @author open2code
 */
namespace App\Http\Controllers\MovementTracking;

use App\Http\Controllers\ApiController;
use App\Models\MovementTracking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Place controller first version
 */
class MovementTrackingController extends ApiController
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
            $response = $this->showList(MovementTracking::search($request->get('search'))->orderBy('id','desc')->paginate($paginate));
        } else {
            $response = $this->showList(MovementTracking::orderBy('id','desc')->paginate($paginate));
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
        $this->validate($request, MovementTracking::rules());
        $movementTracking = MovementTracking::create($request->all());

        return $this->showOne($movementTracking);
    }

    /**
     * Display the specified resource.
     *
     * @param MovementTracking $movementTracking
     *
     * @return JsonResponse
     */
    public function show(MovementTracking $movementTracking): JsonResponse
    {
        return $this->showOne($movementTracking);
    }

    /**
     * @param Request $request
     * @param MovementTracking $movementTracking
     *
     * @return JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, MovementTracking $movementTracking): JsonResponse
    {
        $this->validate($request, MovementTracking::rules());
        $movementTracking->fill($request->all());
        if ($movementTracking->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $movementTracking->save();

        return $this->showOne($movementTracking);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param MovementTracking $movementTracking
     *
     * @return JsonResponse
     */
    public function destroy(MovementTracking $movementTracking): JsonResponse
    {
        $movementTracking->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
