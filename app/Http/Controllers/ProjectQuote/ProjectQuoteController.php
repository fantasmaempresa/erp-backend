<?php
/*
 * CODE
 * ProjectQuote Controller
*/
namespace App\Http\Controllers\ProjectQuote;

use App\Events\NotificationEvent;
use App\Events\QuoteEvent;
use App\Models\Role;
use App\Models\StatusQuote;
use App\Models\User;
use App\Notifications\QuoteNotification;
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
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        return $this->showList(
            ProjectQuote::with('user')
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
    public function store(Request $request): JsonResponse
    {
        $this->validate($request, ProjectQuote::rules());
        $projectQuote = new ProjectQuote($request->all());
        // phpcs:ignore
        $projectQuote->user_id = Auth::id();
        // phpcs:ignore
        $projectQuote->status_quote_id = StatusQuote::$START;

        if ($projectQuote->save()) {
            if ($request->has('concepts')) {
                $concepts = $request->get('concepts');
                foreach ($concepts as $concept) {
                    $projectQuote->concept()->attach($concept['id']);
                }
            }
            $notification = $this->createNotification(ProjectQuote::getMessageNotify(StatusQuote::$START, $projectQuote->name), null, Role::$ADMIN);

            $this->sendNotification(
                $notification,
                new QuoteNotification(User::findOrFail(Auth::id())),
                new NotificationEvent($notification, 0, Role::$ADMIN, [])
            );
        }


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

        if ($request->has('concepts')) {
            $concepts = $request->get('concepts');
            $ids = [];
            foreach ($concepts as $concept) {
                $ids[] = $concept['id'];
            }

            $projectQuote->concept()->sync($ids);
        }
        $projectQuote->concept;

        $notification = $this->createNotification(ProjectQuote::getMessageNotify($request->get('status_quote_id'), $projectQuote->name), null, Role::$ADMIN);

        $this->sendNotification(
            $notification,
            new QuoteNotification(User::findOrFail(Auth::id())),
            new NotificationEvent($notification, 0, Role::$ADMIN, [])
        );


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
