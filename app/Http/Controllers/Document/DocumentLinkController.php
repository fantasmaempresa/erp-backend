<?php

/*
 * CODE
 * Document Link Controller
*/

namespace App\Http\Controllers\Document;

use App\Http\Controllers\ApiController;
use App\Models\Client;
use App\Models\ClientLink;
use App\Models\Document;
use App\Models\Procedure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use PHPUnit\Exception;

/**
 * @access  public
 *
 * @version 1.0
 */
class DocumentLinkController extends ApiController
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function index(Request $request): JsonResponse
    {
        $this->validate($request, [
            'client_id' => 'required|int', // Cuando el view es procedure, el cliente_id es el id del proceso ligado
            'view' => 'required|string'
        ]);

        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');


        if ($request->get('view') == 'client') {
            $client = Client::findOrFail($request->get('client_id'));

            $documents = $client->documents()->withPivot('file')->get();
            $expedient = $documents->map(function ($document) use ($client) {
                $document->url = url('storage/app/clients/' . $client->id . '/expedient/' . $document->pivot->file);
                return $document;
            });
        } elseif ($request->get('view') == 'client_link') {
            $client = ClientLink::findOrFail($request->get('client_id'));

            $documents = $client->documents()->withPivot('file')->get();
            $expedient = $documents->map(function ($document) use ($client) {
                $document->url = url('storage/app/clients_link/' . $client->id . '/expedient/' . $document->pivot->file);
                return $document;
            });
        } elseif ($request->get('view') == 'procedures') {
            $procedure = Procedure::findOrFail($request->get('client_id'));

            $documents = $procedure->documents()->withPivot('file')->get();
            $expedient = $documents->map(function ($document) use ($procedure) {
                $document->url = url('storage/app/procedures/' . $procedure->id . '/expedient/' . $document->pivot->file);
                return $document;
            });
        } else {
            return $this->errorResponse('value view not correct', 409);
        }


        $currentPage = Paginator::resolveCurrentPage('page');
        $paginatedExpedient = new LengthAwarePaginator(
            $expedient->forPage($currentPage, $paginate),
            $expedient->count(),
            $paginate,
            $currentPage,
            ['path' => Paginator::resolveCurrentPath()]
        );

        return $this->showList($paginatedExpedient);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'client_id' => 'required|int',
            'document_id' => 'required|int',
            'view' => 'required|string',
            'file' => 'required|file|max:20048'
        ]);


        if ($request->get('view') == 'client') {
            $client = Client::findOrFail($request->get('client_id'));
            $path = 'clients/' . $client->id . '/expedient/';

        } elseif ($request->get('view') == 'client_link') {
            $client = ClientLink::findOrFail($request->get('client_id'));
            $path = 'clients_link/' . $client->id . '/expedient/';

        } elseif ($request->get('view') == 'procedures') {
            $client = Procedure::findOrFail($request->get('client_id'));
            $path = 'procedures/' . $client->id . '/expedient/';

        } else {
            return $this->errorResponse('value view not correct', 409);
        }

        DB::beginTransaction();
        try {
            $document = Document::findOrFail($request->get('document_id'));
            $file = $request->file('file');
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs($path, $fileName);
            $client->documents()->attach($document->id, ['file' => $fileName]);

            DB::commit();

            return $this->showOne($client);

        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse('error --> ' . $e->getMessage(), 409);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param Client $client
     *
     * @return JsonResponse
     */
    public function show(Client $client): JsonResponse
    {
        $client->clientDocument;

        return $this->showOne($client);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Client $client
     *
     * @return JsonResponse
     */
    public function update(Request $request, Client $client): JsonResponse
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $this->validate($request,
            [
                'view' => 'required|string',
                'document_id' => 'required|int',

            ]
        );

        if ($request->get('view') == 'client') {
            $client = Client::findOrFail($id);
            $path = 'clients/' . $client->id . '/expedient/';

        } elseif ($request->get('view') == 'client_link') {
            $client = ClientLink::findOrFail($id);
            $path = 'clients_link/' . $client->id . '/expedient/';

        } elseif ($request->get('view') == 'procedure') {
            $client = Procedure::findOrFail($id);
            $path = 'procedures/' . $client->id . '/expedient/';

        } else {
            return $this->errorResponse('value view not correct', 409);
        }

        DB::beginTransaction();
        try {

            $client->documents()->detach();
        } catch (Exception $e) {
            DB::rollBack();

            return $this->errorResponse('error --> ' . $e->getMessage(), 409);
        }
    }
}
