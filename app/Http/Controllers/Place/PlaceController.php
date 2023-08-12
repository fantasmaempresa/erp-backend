<?php
/**
 * @author open2code
 */
namespace App\Http\Controllers\Place;

use App\Http\Controllers\ApiController;
use App\Models\Place;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Place controller first version
 */
class PlaceController extends ApiController
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        if ($request->has('search')) {
            $response = $this->showList(Place::search($request->get('search'))->paginate($paginate));
        } else {
            $response = $this->showList(Place::paginate($paginate));
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
        $this->validate($request, Place::rules());
        $place = Place::create($request->all());

        return $this->showOne($place);
    }

    /**
     * Display the specified resource.
     *
     * @param Place $place
     *
     * @return JsonResponse
     */
    public function show(Place $place): JsonResponse
    {
        return $this->showOne($place);
    }

    /**
     * @param Request $request
     * @param Place $place
     *
     * @return JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Place $place): JsonResponse
    {
        $this->validate($request, Place::rules());
        $place->fill($request->all());
        if ($place->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $place->save();

        return $this->showOne($place);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Place $place
     *
     * @return JsonResponse
     */
    public function destroy(Place $place): JsonResponse
    {
        $place->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
