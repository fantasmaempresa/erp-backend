<?php
/**
 * open2code
 */

namespace App\Http\Controllers\Grantor;

use App\Http\Controllers\ApiController;
use App\Models\Grantor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Grantor Controller first version
 */
class GrantorController extends ApiController
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
            $response = $this->showList(Grantor::search($request->get('search'))->paginate($paginate));
        } else {
            $response = $this->showList(Grantor::paginate($paginate));
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
        $this->validate($request, Grantor::rules());
        $grantor = Grantor::create($request->all());

        return $this->showOne($grantor);
    }

    /**
     * Display the specified resource.
     *
     * @param Grantor $grantor
     *
     * @return JsonResponse
     */
    public function show(Grantor $grantor)
    {
        return $this->showOne($grantor);
    }


    /**
     * @param Request $request
     * @param Grantor $grantor
     *
     * @return JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Grantor $grantor): JsonResponse
    {
        $this->validate($request, Grantor::rules());
        $grantor->fill($request->all());
        if ($grantor->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $grantor->save();

        return $this->showOne($grantor);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Grantor $grantor
     *
     * @return JsonResponse
     */
    public function destroy(Grantor $grantor)
    {
        $grantor->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
