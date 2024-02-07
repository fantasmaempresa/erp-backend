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
    public function index(Request $request): JsonResponse
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        if (!empty($request->get('search')) && $request->get('search') !== 'null') {
            $response = $this->showList(WorkArea::search($request->get('search'))->paginate($paginate));
        } else {
            $response = $this->showList(WorkArea::paginate($paginate));
        }

        return $response;
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
        $this->validate($request, WorkArea::rules());
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
     * @param Request  $request
     * @param WorkArea $workArea
     *
     * @return JsonResponse
     */
    public function update(Request $request, WorkArea $workArea): JsonResponse
    {
        $this->validate($request, WorkArea::rules());
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
