<?php
/*
 * OPEN2CODE
 */
namespace App\Http\Controllers\Alienating;

use App\Http\Controllers\ApiController;
use App\Models\Alienating;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * @version1
 */
class AlienatingController extends ApiController
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

        return $this->showList(Alienating::paginate($paginate));
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
        $this->validate($request, Alienating::rules());
        $alienating = Alienating::create($request->all());

        return $this->showOne($alienating);
    }

    /**
     * Display the specified resource.
     *
     *
     * @param ALienating $alienating
     *
     * @return JsonResponse
     */
    public function show(Alienating $alienating): JsonResponse
    {
        return $this->showOne($alienating);
    }

    /**
     * @param Request    $request
     * @param Alienating $alienating
     *
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function update(Request $request, Alienating $alienating): JsonResponse
    {
        $this->validate($request, Alienating::rules($alienating->id));
        $alienating->fill($request->all());
        if ($alienating->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $alienating->save();

        return $this->showOne($alienating);
    }

    /**
     * @param Alienating $alienating
     *
     * @return JsonResponse
     */
    public function destroy(Alienating $alienating): JsonResponse
    {
        $alienating->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
