<?php

/*
 * CODE
 * Document Link Controller
*/

namespace App\Http\Controllers\Document;

use App\Http\Controllers\ApiController;
use App\Models\Client;
use App\Models\ClientDocument;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Psy\Util\Json;

/**
 * @access  public
 *
 * @version 1.0
 */
class DocumentLinkController extends ApiController
{

    public function index(Request $request)
    {
        $this->validate($request, [
            'client_id' => 'required|int'
        ]);

        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        $client = Client::findOrFail($request->get('client_id'));
        $expedient = $client->documents()->paginate();

        return $this->showList($expedient);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'client_id' => 'required|int',
            'document_id' => 'required|int',
            'file' => 'required|file|max:20048'
        ]);

        DB::beginTransaction();
        try {
            $client = Client::findOrFail($request->get('client_id'));
            $document = Document::findOrFail($request->get('document_id'));
            $client->clientDocument()->attach($document->id);
            $file = $request->file('file');
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('clients/' . $client->id . '/expedient/', $fileName);
            $documentLink = ClientDocument::where('client_id', $client->id)->where('document_id', $request->get('document_id'))->first();
            $documentLink->file = $fileName;
            $documentLink->save();
            DB::commit();

            return $this->showOne($client);

        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse('Ocurrio un error inesperado');
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
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
