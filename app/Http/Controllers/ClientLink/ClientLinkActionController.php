<?php

/*
 * CLIENT LINK ACTION CONTROLLER
 */
namespace App\Http\Controllers\ClientLink;

use App\Http\Controllers\ApiController;
use App\Models\ClientLink;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @version
 */
class ClientLinkActionController extends ApiController
{

    /**
     * @param ClientLink $clientLink
     *
     * @return JsonResponse
     */
    public function active(ClientLink $clientLink): JsonResponse
    {
        $clientLinks = ClientLink::where('client_id', $clientLink->client_id)->get();

        foreach ($clientLinks as $link) {
            $link->active = ClientLink::INACTIVE;
            $link->save();
        }

        $clientLink->active = ClientLink::ACTIVE;
        $clientLink->save();

        return $this->showOne($clientLink);
    }
}
