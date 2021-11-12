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
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->showList(Client::paginate(100));
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
        $rules = [
            'name' => 'require|string',
            'email' => 'require|email',
            'phone' => 'require|string|max:10|min:10',
            'nickname' => 'nullable|string ',
            'address' => 'require|string',
            'rfc' => 'require|string|max:13|min:10',
        ];

        $this->validate($request, $rules);
        $client = Client::create($request->all());
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
     */
    public function update(Request $request, Client $client): JsonResponse
    {
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
