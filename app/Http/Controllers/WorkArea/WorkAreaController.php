<?php

/*
 * CODE
 * WorkArea Controller
*/

namespace App\Http\Controllers\WorkArea;

use Exception;
use App\Models\WorkArea;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class WorkAreaController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $workAreas = WorkArea::all();

        return $this->showAll($workAreas);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $rules = [];

        $this->validate($request, $rules);
        $workArea = WorkArea::create($request->all());

        return $this->showOne($workArea);
    }

    /**
     * @param WorkArea $workArea
     *
     * @return JsonResponse
     */
    public function show(WorkArea $workArea): JsonResponse
    {
        return $this->showOne($workArea);
    }

    /**
     * @param Request $request
     * @param WorkArea $workArea
     *
     * @return JsonResponse
     */
    public function update(Request $request, WorkArea $workArea): JsonResponse
    {
        $workArea->fill($request->all());
        if ($workArea->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $workArea->save();

        return $this->showOne($workArea);
    }

    /**
     * @param WorkArea $workArea
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(WorkArea $workArea): JsonResponse
    {
        $workArea->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
