<?php

/*
 * CODE
 * DetailProject Controller
*/

namespace App\Http\Controllers\DetailProject;

use Exception;
use App\Models\DetailProject;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class DetailProjectController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        if (!empty($request->get('search')) && $request->get('search') !== 'null') {
            $response = $this->showList(DetailProject::search($request->get('search'))->orderBy('id','desc')->paginate($paginate));
        } else {
            $response = $this->showList(DetailProject::orderBy('id','desc')->paginate($paginate));
        }

        return $this->showList($response);
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
        $this->validate($request, DetailProject::rules());
        $detailProject = DetailProject::create($request->all());
        $detailProject->phase;

        return $this->showOne($detailProject);
    }

    /**
     * @param DetailProject $detailProject
     *
     * @return JsonResponse
     */
    public function show(DetailProject $detailProject): JsonResponse
    {
        return $this->showOne($detailProject);
    }

    /**
     * @param Request       $request
     * @param DetailProject $detailProject
     *
     * @return JsonResponse
     */
    public function update(Request $request, DetailProject $detailProject): JsonResponse
    {
        $this->validate($request, DetailProject::rules());
        $detailProject->fill($request->all());
        if ($detailProject->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $detailProject->save();

        return $this->showOne($detailProject);
    }

    /**
     * @param DetailProject $detailProject
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
//    public function destroy(DetailProject $detailProject): JsonResponse
//    {
//        $detailProject->delete();
//
//        return $this->showMessage('Record deleted successfully');
//    }
}
