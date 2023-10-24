<?php
/*
 * OPEN2CODE
 */
namespace App\Http\Controllers\Acquirer;

use App\Http\Controllers\ApiController;
use App\Models\Acquirer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * @version1
 */
class AcquirerController extends ApiController
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

        return $this->showList(Acquirer::paginate($paginate));
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
        $this->validate($request, Acquirer::rules());
        $alienating = Acquirer::create($request->all());

        return $this->showOne($alienating);
    }

    /**
     * Display the specified resource.
     *
     * @param Acquirer $acquirer
     *
     * @return JsonResponse
     */
    public function show(Acquirer $acquirer): JsonResponse
    {
        return $this->showOne($acquirer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request  $request
     * @param Acquirer $acquirer
     *
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function update(Request $request, Acquirer $acquirer): JsonResponse
    {
        $this->validate($request, Acquirer::rules($acquirer->id));
        $acquirer->fill($request->all());
        if ($acquirer->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $acquirer->save();

        return $this->showOne($acquirer);
    }

    /**
     * @param Acquirer $acquirer
     *
     * @return JsonResponse
     */
    public function destroy(Acquirer $acquirer): JsonResponse
    {
        $acquirer->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
