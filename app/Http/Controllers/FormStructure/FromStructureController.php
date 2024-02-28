<?php
/*
 * CODE
 * Form Structure Controller
*/

namespace App\Http\Controllers\FormStructure;

use App\Http\Controllers\ApiController;
use App\Models\FormStructure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class FromStructureController extends ApiController
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        if ($request->has('search')) {
            $response = $this->showList(FormStructure::search($request->get('search'))->orderBy('id','desc')->paginate($paginate));
        } else {
            $response = $this->showList(FormStructure::orderBy('id','desc')->paginate($paginate));
        }

        return $response;
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
        $this->validate($request, FormStructure::rules());
        $formStructure = FormStructure::create($request->all());

        return $this->showOne($formStructure);
    }

    /**
     * Display the specified resource.
     *
     * @param FormStructure $formStructure
     *
     * @return JsonResponse
     */
    public function show(FormStructure $formStructure): JsonResponse
    {
        return $this->showOne($formStructure);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request       $request
     * @param FormStructure $formStructure
     *
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function update(Request $request, FormStructure $formStructure): JsonResponse
    {
        $this->validate($request, FormStructure::rules());
        $formStructure->fill($request->all());
        if ($formStructure->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $formStructure->save();

        return $this->showOne($formStructure);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param FormStructure $formStructure
     *
     * @return JsonResponse
     */
    public function destroy(FormStructure $formStructure): JsonResponse
    {
        $formStructure->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
