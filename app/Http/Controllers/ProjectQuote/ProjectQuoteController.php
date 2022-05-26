<?php
/*
 * CODE
 * ProjectQuote Controller
*/

namespace App\Http\Controllers\ProjectQuote;

use App\Events\NotificationEvent;
use App\Events\QuoteEvent;
use App\Http\Controllers\ApiController;
use App\Models\ProjectQuote;
use App\Models\Role;
use App\Models\StatusQuote;
use App\Models\User;
use App\Notifications\QuoteNotification;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
     *
     * @throws ValidationException
     */
    public function index(Request $request): JsonResponse
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        $projectQuotes = new ProjectQuote();

        $this->validate($request, [
            'date1' => 'date',
            'date2' => 'date|after_or_equal:date1',
        ]);

        if ($request->has('client_id')) {
            $projectQuotes = $projectQuotes->where('client_id', $request->get('client_id'));
        }

        if ($request->has('status_quote_id')) {
            $projectQuotes = $projectQuotes->where('status_quote_id', $request->get('status_quote_id'));
        }

        if ($request->has('date1') && $request->has('date2')) {
            $projectQuotes = $projectQuotes->whereBetween(
                'created_at',
                [$request->get('date1') . ' 0:00:00', $request->get('date2') . ' 23:59:59']
            );
        }

        return $this->showList(
            $projectQuotes->with('user')
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
        //TODO crear validación para ver si los conceptos existen y pueda generar el error controlado y no la excepción de base de datos
        $this->validate($request, ProjectQuote::rules());
        $projectQuote = new ProjectQuote($request->all());
        // phpcs:ignore
        $projectQuote->user_id = Auth::id();
        // phpcs:ignore
        $projectQuote->status_quote_id = StatusQuote::$START;

        if ($projectQuote->save()) {
            if ($request->has('quote') && !empty($request->get('quote'))) {
                if (!empty($request->get('quote')['operations']['operation_fields'])) {
                    foreach ($request->get('quote')['operations']['operation_fields'] as $fields) {
                        foreach ($fields['concepts'] as $field) {
                            $projectQuote->concept()->attach($field["id"]);
                        }
                    }

                    foreach ($request->get('quote')['operations']['operation_total'] as $fields) {
                        foreach ($fields['concepts'] as $field) {
                            $projectQuote->concept()->attach($field["id"]);
                        }
                    }
                }
            }
            $notification = $this->createNotification(
                ProjectQuote::getMessageNotify(StatusQuote::$START, $projectQuote->name),
                null,
                Role::$ADMIN
            );

            $this->sendNotification(
                $notification,
                new QuoteNotification(User::findOrFail(Auth::id())),
                new NotificationEvent($notification, 0, Role::$ADMIN, [])
            );
        }

        $projectQuote->concept;

        return $this->showOne($projectQuote);
    }

    /**
     * @param ProjectQuote $projectQuote
     *
     * @return JsonResponse
     */
    public function show(ProjectQuote $projectQuote): JsonResponse
    {
        $projectQuote->user;
        $projectQuote->project;
        $projectQuote->client;
        $projectQuote->statusQuote;
        $projectQuote->concept;

        return $this->showOne($projectQuote);
    }

    /**
     *
     * @param Request $request
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

        // phpcs:ignore
        $projectQuote->save();

        if ($request->has('quote') && !empty($request->get('quote'))) {
            if (!empty($request->get('quote')['operations']['operation_fields'])) {
                $ids = [];
                foreach ($request->get('quote')['operations']['operation_fields'] as $fields) {
                    foreach ($fields['concepts'] as $field) {
                        $ids[] = $field["id"];
                    }
                }
                $projectQuote->concept()->sync($ids);

                $ids = [];
                if ($request->has('quote') && !empty($request->get('quote'))) {
                    if (!empty($request->get('quote')['operations']['operation_fields'])) {
                        foreach ($request->get('quote')['operations']['operation_fields'] as $fields) {
                            foreach ($fields['concepts'] as $field) {
                                $ids[$field["id"]] = $field["id"];
                            }
                        }

                        foreach ($request->get('quote')['operations']['operation_total'] as $fields) {
                            foreach ($fields['concepts'] as $field) {
                                $ids[$field["id"]] = $field["id"];
                            }
                        }
                    }
                }

                $projectQuote->concept()->sync($ids);
            }
        }

        $projectQuote->concept;

        $notification = $this->createNotification(
            ProjectQuote::getMessageNotify($request->get('status_quote_id'), $projectQuote->name),
            null,
            Role::$ADMIN
        );

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
