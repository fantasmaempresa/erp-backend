<?php
/*
 * CODE
 * ProjectQuoteFilter Controller
*/

namespace App\Http\Controllers\ProjectQuote;

use App\Http\Controllers\ApiController;
use App\Models\ProjectQuote;
use App\Models\StatusQuote;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Psy\Util\Json;

/**
 * @access  public
 *
 * @version 1.0
 */
class ProjectQuoteFilterController extends ApiController
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getQuotesStart(Request $request): JsonResponse
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        return $this->showList(
            ProjectQuote::where('status_quote_id', StatusQuote::$START)
                ->orderBy('id', 'DESC')
                ->with('user')
                ->with('project')
                ->with('client')
                ->with('statusQuote')
                ->with('concept')
                ->paginate($paginate)
        );
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getQuotesReview(Request $request): JsonResponse
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        return $this->showList(
            ProjectQuote::where('status_quote_id', StatusQuote::$REVIEW)
                ->orderBy('id', 'DESC')
                ->with('user')
                ->with('project')
                ->with('client')
                ->with('statusQuote')
                ->with('concept')
                ->paginate($paginate)
        );
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getQuotesApproved(Request $request): JsonResponse
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        return $this->showList(
            ProjectQuote::where('status_quote_id', StatusQuote::$APPROVED)
                ->orderBy('id', 'DESC')
                ->with('user')
                ->with('project')
                ->with('client')
                ->with('statusQuote')
                ->with('concept')
                ->paginate($paginate)
        );
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getQuotesFinish(Request $request): JsonResponse
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        return $this->showList(
            ProjectQuote::where('status_quote_id', StatusQuote::$FINISH)
                ->orderBy('id', 'DESC')
                ->with('user')
                ->with('project')
                ->with('client')
                ->with('statusQuote')
                ->with('concept')
                ->paginate($paginate)
        );
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getQuotesByUser(Request $request): JsonResponse
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        return $this->showList(
            ProjectQuote::where('user_id', Auth::id())
                ->orderBy('id', 'DESC')
                ->with('user')
                ->with('project')
                ->with('client')
                ->with('statusQuote')
                ->with('concept')
                ->paginate($paginate)
        );
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function getQuotesUser(Request $request): JsonResponse
    {
        $this->validate($request, ['user_id' => 'required']);

        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        return $this->showList(
            ProjectQuote::where('user_id', $request->get('user_id'))
                ->orderBy('id', 'DESC')
                ->with('user')
                ->with('project')
                ->with('client')
                ->with('statusQuote')
                ->with('concept')
                ->paginate($paginate)
        );
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function getQuotesByClient(Request $request): JsonResponse
    {
        $this->validate($request, ['client_id' => 'required']);

        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        return $this->showList(
            ProjectQuote::where('client_id', $request->get('client_id'))
                ->orderBy('id', 'DESC')
                ->with('user')
                ->with('project')
                ->with('client')
                ->with('statusQuote')
                ->with('concept')
                ->paginate($paginate)
        );
    }
}
