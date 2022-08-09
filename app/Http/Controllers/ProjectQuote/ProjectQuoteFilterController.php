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
        $user = User::findOrFail(Auth::id());

        return $this->showList(
            ProjectQuote::filter('status_quote_id', StatusQuote::$START, $user)
                ->paginate(empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate'))
        );
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getQuotesReview(Request $request): JsonResponse
    {
        $user = User::findOrFail(Auth::id());

        return $this->showList(
            ProjectQuote::filter('status_quote_id', StatusQuote::$REVIEW, $user)
                ->paginate(empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate'))
        );
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getQuotesApproved(Request $request): JsonResponse
    {
        $user = User::findOrFail(Auth::id());

        return $this->showList(
            ProjectQuote::filter('status_quote_id', StatusQuote::$APPROVED, $user)
                ->paginate(empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate'))
        );
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getQuotesFinish(Request $request): JsonResponse
    {
        $user = User::findOrFail(Auth::id());

        return $this->showList(
            ProjectQuote::filter('status_quote_id', StatusQuote::$FINISH, $user)
                ->paginate(empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate'))
        );
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getQuotesByUser(Request $request): JsonResponse
    {
        $user = User::findOrFail(Auth::id());

        return $this->showList(
            ProjectQuote::filter('user_id', Auth::id(), $user)
                ->paginate(empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate'))
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
        $user = User::findOrFail(Auth::id());

        return $this->showList(
            ProjectQuote::filter('user_id', $request->get('user_id'), $user)
                ->paginate(empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate'))
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
        $user = User::findOrFail(Auth::id());

        return $this->showList(
            ProjectQuote::filter('client_id', $request->get('client_id'), $user)
                ->paginate(empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate'))
        );
    }
}
