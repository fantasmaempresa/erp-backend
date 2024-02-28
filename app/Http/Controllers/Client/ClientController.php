<?php

/*
 * CODE
 * Client Controller
*/

namespace App\Http\Controllers\Client;

use Exception;
use App\Models\Client;
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
class ClientController extends ApiController
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        if (!empty($request->get('search')) && $request->get('search') !== 'null') {
            $response = $this->showList(Client::search($request->get('search'))->with('user')->orderBy('id','desc')->paginate($paginate));
        } else {
            $response = $this->showList(Client::with('user')->orderBy('id','desc')->paginate($paginate));
        }

        return $response;
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
        $this->validate($request, Client::rules(id: null,type: $request->get('type')));
        $client = Client::create($request->all());
        // phpcs:ignore
        $client->user_id = Auth::id();
        $client->save();

        return $this->showOne($client);
    }

    /**
     * @param Client $client
     *
     * @return JsonResponse
     */
    public function show(Client $client): JsonResponse
    {
        return $this->showOne($client);
    }

    /**
     * @param Request $request
     * @param Client  $client
     *
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function update(Request $request, Client $client): JsonResponse
    {
        $this->validate($request, Client::rules($client->id, type: $request->get('type')));
        $client->fill($request->all());
        if ($client->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }
        $client->save();

        return $this->showOne($client);
    }

    /**
     * @param Client $client
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Client $client): JsonResponse
    {
        $client->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
