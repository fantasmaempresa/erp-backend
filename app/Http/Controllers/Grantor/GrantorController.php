<?php
/**
 * open2code
 */

namespace App\Http\Controllers\Grantor;

use App\Http\Controllers\ApiController;
use App\Models\Grantor;
use Carbon\Carbon;
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
     * @return JsonResponse0
     */
    public function index(Request $request): JsonResponse
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        if (!empty($request->get('search')) && $request->get('search') !== 'null') {
            $response = $this->showList(Grantor::search($request->get('search'))->orderBy('father_last_name')->with('stake')->paginate($paginate));
        } else {
            $response = $this->showList(Grantor::with('stake')->orderBy('father_last_name')->paginate($paginate));
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
        $this->validate($request, Grantor::rules(null, $request->get('type')));
        $grantor = new Grantor($request->all());
        if(!empty($grantor->birthdate)){
            $grantor->birthdate = Carbon::parse($grantor->birthdate);
        }
        $grantor->save();

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
        $this->validate($request, Grantor::rules($grantor->id, $request->get('type')));
        $grantor->fill($request->all());
        if ($grantor->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        if(!empty($request->get('birthdate'))){
            $grantor->birthdate = Carbon::parse($request->get('birthdate'));
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
