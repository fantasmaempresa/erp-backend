<?php

/*
 * CODE
 * Document Link Controller
*/

namespace App\Http\Controllers\Document;

use App\Http\Controllers\ApiController;
use App\Models\Client;
use App\Models\ClientDocument;
use App\Models\ClientLink;
use App\Models\ClientLinkDocument;
use App\Models\Document;
use App\Models\DocumentProcedure;
use App\Models\DocumentProcessingIncome;
use App\Models\DocumentVulnerableOperation;
use App\Models\Procedure;
use App\Models\ProcessingIncome;
use App\Models\VulnerableOperation;
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

            $documents = $client->documents()->withPivot('file')->withPivot('id')->get();
            $expedient = $documents->map(function ($document) use ($client) {
                $document->url = url('storage/app/clients/' . $client->id . '/expedient/' . $document->pivot->file);
                return $document;
            });
        } elseif ($request->get('view') == 'client_link') {
            $client = ClientLink::findOrFail($request->get('client_id'));

            $documents = $client->documents()->withPivot('file')->withPivot('id')->get();
            $expedient = $documents->map(function ($document) use ($client) {
                $document->url = url('storage/app/clients_link/' . $client->id . '/expedient/' . $document->pivot->file);
                return $document;
            });
        } elseif ($request->get('view') == 'procedures') {
            $procedure = Procedure::findOrFail($request->get('client_id'));

            $documents = $procedure->documents()->withPivot('file')->withPivot('id')->get();
            $expedient = $documents->map(function ($document) use ($procedure) {

                if (empty($document->pivot->file)) {
                    $document->url = null;
                } else {
                    $document->url = url('storage/app/procedures/' . $procedure->id . '/expedient/' . $document->pivot->file);
                }

                return $document;
            });
        } else if ($request->get('view') == 'incomming') {
            $incoming = ProcessingIncome::findOrFail($request->get('client_id'));
            $documents = $incoming->documents()->withPivot('file')->withPivot('id')->get();
            $expedient = $documents->map(function ($document) use ($incoming) {
                if (empty($document->pivot->file)) {
                    $document->url = null;
                } else {
                    $document->url = url('storage/app/incomming/' . $incoming->id . '/expedient/' . $document->pivot->file);
                }
                return $document;
            });
        } else if ($request->get('view') == 'vulnerable_operation') {
            $vulnerableOperation = VulnerableOperation::findOrFail($request->get('client_id'));
            $documents = $vulnerableOperation->documents()->withPivot('file')->withPivot('id')->get();
            $expedient = $documents->map(function ($document) use ($vulnerableOperation) {
                if(empty($document->pivot->file)){
                    $document->url = null;
                }else{
                    $document->url = url('storage/app/vulnerable_operation/' . $vulnerableOperation->id . '/expedient/' . $document->pivot->file);
                }

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
            'file' => 'required|file|max:60048'
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
        } else if ($request->get('view') == 'incomming') {
            $client = ProcessingIncome::findOrFail($request->get('client_id'));
            $path = 'incomming/' . $client->id . '/expedient/';
        } else if ($request->get('view') == 'vulnerable_operation') {
            $client = VulnerableOperation::findOrFail($request->get('client_id'));
            $path = 'vulnerable_operation/' . $client->id . '/expedient/';
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
     * @param $id
     *
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        return $this->showList([$request->all(), $id]);

        $this->validate($request, [
            'client_id' => 'required|int',
            'document_id' => 'required|int',
            'view' => 'required|string',
            'file' => 'required|file|max:60048'
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
        } elseif ($request->get('view') == 'incomming') {
            $client = ProcessingIncome::findOrFail($request->get('client_id'));
            $path = 'incomming/' . $client->id . '/expedient/';
        } else if ($request->get('view') == 'vulnerable_operation') {
            $client = VulnerableOperation::findOrFail($request->get('client_id'));
            $path = 'vulnerable_operation/' . $client->id . '/expedient/';
        } else {
            return $this->errorResponse('value view not correct', 409);
        }

        DB::beginTransaction();
        try {
            $document = Document::findOrFail($request->get('document_id'));
            $file = $request->file('file');
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs($path, $fileName);
            $client->documents()->sync([$document->id], ['file' => $fileName]);

            DB::commit();

            return $this->showOne($client);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse('error --> ' . $e->getMessage(), 409);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id //este id es el id del pivot
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $this->validate(
            $request,
            [
                'view' => 'required|string',
                'client_id' => 'required|int',
            ]
        );

        if ($request->get('view') == 'client') {
            //$client = Client::findOrFail($request->get('client_id'));
            $pivot = ClientDocument::findOrFail($id);
        } elseif ($request->get('view') == 'client_link') {
            //$client = ClientLink::findOrFail($request->get('client_id'));
            $pivot = ClientLinkDocument::findOrFail($id);
        } elseif ($request->get('view') == 'procedures') {
            //$client = Procedure::findOrFail($request->get('client_id'));
            $pivot = DocumentProcedure::findOrFail($id);
        } elseif ($request->get('view') == 'incomming') {
            //$client = Procedure::findOrFail($request->get('client_id'));
            $pivot = DocumentProcessingIncome::findOrFail($id);
        } else if ($request->get('view') == 'vulnerable_operation') {
            $pivot = VulnerableOperation::findOrFail($id);
        } else {
            return $this->errorResponse('value view not correct', 409);
        }


        DB::beginTransaction();
        try {

            $pivot->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return $this->errorResponse('error --> ' . $e->getMessage(), 409);
        }
    }

    public function updateAlternative(Request $request): JsonResponse
    {
        $this->validate($request, [
            'client_id' => 'required|int',
            'document_id' => 'required|int',
            'view' => 'required|string',
            'file' => 'required|file|max:60048',
            'document_pivot_id' => 'required|int'
        ]);

        if ($request->get('view') == 'client') {
            $client = Client::findOrFail($request->get('client_id'));
            $path = 'clients/' . $client->id . '/expedient/';
            $pivot = ClientDocument::findOrFail($request->get('document_pivot_id'));
        } elseif ($request->get('view') == 'client_link') {
            $client = ClientLink::findOrFail($request->get('client_id'));
            $path = 'clients_link/' . $client->id . '/expedient/';
            $pivot = ClientLinkDocument::findOrFail($request->get('document_pivot_id'));
        } elseif ($request->get('view') == 'procedures') {
            $client = Procedure::findOrFail($request->get('client_id'));
            $path = 'procedures/' . $client->id . '/expedient/';
            $pivot = DocumentProcedure::findOrFail($request->get('document_pivot_id'));
        } elseif ($request->get('view') == 'incomming') {
            $client = ProcessingIncome::findOrFail($request->get('client_id'));
            $path = 'incomming/' . $client->id . '/expedient/';
            $pivot = DocumentProcessingIncome::findOrFail($request->get('document_pivot_id'));
            $client->notify($request->get('document_id'));
        } else if ($request->get('view') == 'vulnerable_operation') {
            $client = VulnerableOperation::findOrFail($request->get('client_id'));
            $path = 'vulnerable_operation/' . $client->id . '/expedient/';
            $pivot = DocumentVulnerableOperation::findOrFail($request->get('document_pivot_id'));
        } else {
            return $this->errorResponse('value view not correct', 409);
        }
        DB::beginTransaction();
        try {
            $file = $request->file('file');
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs($path, $fileName);
            $pivot->file = $fileName;
            $pivot->save();
            DB::commit();
            return $this->showOne($pivot);
        } catch (Exception $e) {
            DB::rollBack();

            return $this->errorResponse('error --> ' . $e->getMessage(), 409);
        }
    }
}
