<?php

/*
 * CODE
 * Client Link Controller
*/

namespace App\Http\Controllers\ClientLink;

use App\Http\Controllers\ApiController;
use App\Models\Client;
use App\Models\ClientLink;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class ClientLinkController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function index(Request $request): JsonResponse
    {
        $this->validate($request, ['client_id' => 'required']);

        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        if (!empty($request->get('search')) && $request->get('search') !== 'null') {
            $clientLinks = ClientLink::where('client_id', $request->get('client_id'))->search($request->get('search'))->with('user')->paginate($paginate);
        } else {
            $clientLinks = ClientLink::where('client_id', $request->get('client_id'))->with('user')->paginate($paginate);
        }

        return $this->showList($clientLinks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $this->validate($request, ClientLink::rules());

        $client = Client::findOrFail($request->get('client_id'));
        // phpcs:ignore
        if ($client->type != Client::MORAL_PERSON) {
            return $this->errorResponse('This client is not a moral person', 415);
        }

        $clientLink = ClientLink::create($request->all());
        // phpcs:ignore
        $clientLink->user_id = Auth::id();
        $clientLink->save();

        return $this->showOne($clientLink);
    }

    /**
     * Display the specified resource.
     *
     * @param ClientLink $clientLink
     *
     * @return JsonResponse
     */
    public function show(ClientLink $clientLink): JsonResponse
    {
        return $this->showOne($clientLink);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request    $request
     *
     * @param ClientLink $clientLink
     *
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function update(Request $request, ClientLink $clientLink): JsonResponse
    {
        $this->validate($request, ClientLink::rules($clientLink->id));
        $clientLink->fill($request->all());
        if ($clientLink->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $client = Client::findOrFail($request->get('client_id'));
        // phpcs:ignore
        if ($client->type != Client::MORAL_PERSON) {
            return $this->errorResponse('This client is not a moral person', 415);
        }

        $clientLink->save();

        return $this->showOne($clientLink);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ClientLink $clientLink
     *
     * @return JsonResponse
     */
    public function destroy(ClientLink $clientLink): JsonResponse
    {
        $clientLink->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
