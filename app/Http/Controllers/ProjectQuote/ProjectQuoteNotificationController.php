<?php

/*
 * CODE
 * ProjectQuote Controller
*/

namespace App\Http\Controllers\ProjectQuote;

use App\Http\Controllers\ApiController;
use App\Models\ProjectQuote;
use App\Models\StatusQuote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @acc ess  public
 *
 * @version 1.0
 */
class ProjectQuoteNotificationController extends ApiController
{

    /**
     * @param ProjectQuote $projectQuote
     * @param StatusQuote  $statusQuote
     *
     * @return JsonResponse
     */
    public function notificationStatusQuote(ProjectQuote $projectQuote, StatusQuote $statusQuote): JsonResponse
    {
        // phpcs:ignore
        $projectQuote->status_quote_id = $statusQuote->id;
        $projectQuote->save();

        //TODO mandar evento de actuailzación de cotización y mandar email a los administradores
        return $this->showMessage("Success update status");
    }

    /**
     * @param Request      $request
     * @param ProjectQuote $projectQuote
     *
     * @return JsonResponse
     */
    public function notificationAssignQuote(Request $request, ProjectQuote $projectQuote): JsonResponse
    {
        if ($request->has('project_id')) {
            // phpcs:ignore
            $projectQuote->project_id = $request->get('project_id');
        }

        if ($request->has('client_id')) {
            // phpcs:ignore
            $projectQuote->client_id = $request->get('client_id');
        }

        //TODO agragar evento para notificar que se cambio el estatus de una cotización
        $projectQuote->save();

        return $this->showOne($projectQuote);
    }
}
