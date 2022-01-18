<?php

/*
 * CODE
 * ProjectQuote Controller
*/

namespace App\Http\Controllers\ProjectQuote;

use Exception;
use App\Models\ProjectQuote;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class ProjectQuoteController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->showList(
            ProjectQuote::with('user')
                ->with('project')
                ->with('client')
                ->with('statusQuote')
                ->paginate(env('NUMBER_PAGINATE'))
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
        $this->validate($request, ProjectQuote::rules());
        $projectQuote = ProjectQuote::create($request->all());
        // phpcs:ignore
//        $projectQuote->user_id = Auth::id();
//        $projectQuote->save();

        return $this->showOne($projectQuote);
    }

    /**
     * @param ProjectQuote $projectQuote
     *
     * @return JsonResponse
     */
    public function show(ProjectQuote $projectQuote): JsonResponse
    {
        return $this->showOne($projectQuote);
    }

    /**
     * @param Request      $request
     * @param ProjectQuote $projectQuote
     *
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function update(Request $request, ProjectQuote $projectQuote): JsonResponse
    {
        $this->validate($request, ProjectQuote::rules());
        $projectQuote->fill($request->all());
        if ($projectQuote->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $projectQuote->save();

        return $this->showOne($projectQuote);
    }

    /**
     * @param ProjectQuote $projectQuote
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(ProjectQuote $projectQuote): JsonResponse
    {
        $projectQuote->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
