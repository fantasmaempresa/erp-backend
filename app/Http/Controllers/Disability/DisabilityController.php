<?php

/*
 * CODE
 * Disability Controller
*/

namespace App\Http\Controllers\Disability;

use Exception;
use App\Models\Disability;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class DisabilityController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $disabilities = Disability::all();

        return $this->showAll($disabilities);
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
        $this->validate($request, Disability::rules());
        $disability = Disability::create($request->all());

        return $this->showOne($disability);
    }

    /**
     * @param Disability $disability
     *
     * @return JsonResponse
     */
    public function show(Disability $disability): JsonResponse
    {
        return $this->showOne($disability);
    }

    /**
     * @param Request    $request
     * @param Disability $disability
     *
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function update(Request $request, Disability $disability): JsonResponse
    {
        $this->validate($request, Disability::rules());
        $disability->fill($request->all());
        if ($disability->isClean()) {
            return $this->errorResponse(
                'A different value must be specified to update',
                422
            );
        }

        $disability->save();

        return $this->showOne($disability);
    }

    /**
     * @param Disability $disability
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Disability $disability): JsonResponse
    {
        $disability->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
