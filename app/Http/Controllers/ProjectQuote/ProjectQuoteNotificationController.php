<?php

/*
 * CODE
 * ProjectQuote Controller
*/

namespace App\Http\Controllers\ProjectQuote ;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @access  public
 *
 * @version 1.0
 */
class ProjectQuoteNotificationController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function notificationClient(): JsonResponse
    {
        return $this->showList([]);
    }
}
