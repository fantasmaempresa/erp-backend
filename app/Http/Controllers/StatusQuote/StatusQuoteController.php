<?php

/*
 * CODE
 * StatusQuote Controller
*/

namespace App\Http\Controllers\StatusQuote;

use Exception;
use App\Models\StatusQuote;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class StatusQuoteController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->showList(
            StatusQuote::orderBy('id','desc')->paginate(env('NUMBER_PAGINATE'))
        );
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
        $this->validate($request, StatusQuote::rules());
        $statusQuote = StatusQuote::create($request->all());

        return $this->showOne($statusQuote);
    }

    /**
     * @param StatusQuote $statusQuote
     *
     * @return JsonResponse
     */
    public function show(StatusQuote $statusQuote): JsonResponse
    {
        return $this->showOne($statusQuote);
    }

    /**
     * @param Request     $request
     * @param StatusQuote $statusQuote
     *
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function update(Request $request, StatusQuote $statusQuote): JsonResponse
    {
        $this->validate($request, StatusQuote::rules());
        $statusQuote->fill($request->all());
        if ($statusQuote->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $statusQuote->save();

        return $this->showOne($statusQuote);
    }

    /**
     * @param StatusQuote $statusQuote
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(StatusQuote $statusQuote): JsonResponse
    {
        $statusQuote->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
