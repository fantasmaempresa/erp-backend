<?php

namespace App\Http\Controllers\Document;

use App\Http\Controllers\ApiController;
use App\Models\Procedure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class DocumentActionControler extends ApiController
{

    public function getAppendix(Request $request): JsonResponse
    {

        $this->validate($request, [
            'procedure_id' => 'required|exists:procedures,id',
        ]);
        
        $procedure = Procedure::findOrFail($request->get('procedure_id'));

        if (empty($procedure->folio->id) || $procedure->status != Procedure::ACCEPTED) {
            return $this->errorResponse('Aún no se pude generar el apendice de este expediente', 422);
        }        

        $documents = collect();

        // Documentos del procedimiento
        $procedureDocuments = $procedure->documents()->withPivot('file')->withPivot('id')->get();
        $documents = $documents->merge($procedureDocuments->map(function ($document) use ($procedure) {
            $document->url = $this->getDocumentUrl($document, $procedure->id, 'procedures');
            return $document;
        }));


        // Documentos del cliente
        $clientDocuments = $procedure->client->documents()->withPivot('file')->withPivot('id')->get();
        $documents = $documents->merge($clientDocuments->map(function ($document) use ($procedure) {
            $document->url = $this->getDocumentUrl($document, $procedure->client->id, 'clients');
            return $document;
        }));

        // Documentos de operación vulnerable
        if ($procedure->vulnerableOperation()->count() > 0) {
            $vulnerableOperationDocuments = $procedure->vulnerableOperation->documents()->withPivot('file')->withPivot('id')->get();
            $documents = $documents->merge($vulnerableOperationDocuments->map(function ($document) use ($procedure) {
                $document->url = $this->getDocumentUrl($document, $procedure->vulnerableOperation->id, 'vulnerable_operation');
                return $document;
            }));
        }

        // Documentos de cliente link
        if ($procedure->client->clientLink()->count() > 0) {
            $clientLinkDocuments = $procedure->client->clientLink()->first()->documents()->withPivot('file')->withPivot('id')->get();
            $documents = $documents->merge($clientLinkDocuments->map(function ($document) use ($procedure) {
                $document->url = $this->getDocumentUrl($document, $procedure->client->clientLink->id, 'clients_link');
                return $document;
            }));
        }

        // Documentos de ingresos
        if ($procedure->processingIncome()->count() > 0) {
            foreach ($procedure->processingIncome as $incoming) {
                $incomingDocuments = $incoming->documents()->withPivot('file')->withPivot('id')->get();
                $documents = $documents->merge($incomingDocuments->map(function ($document) use ($incoming) {
                    $document->url = $this->getDocumentUrl($document, $incoming->id, 'incomming');
                    return $document;
                }));
            }
        }


        $page = $request->input('page', 1);
        $offset = ($page - 1) * env('NUMBER_PAGINATE');
        $itemsPaginados = array_slice($documents->toArray(), $offset, env('NUMBER_PAGINATE'));
        $paginador = new LengthAwarePaginator($itemsPaginados, $documents->count(), env('NUMBER_PAGINATE'), $page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        return $this->showList($paginador);
    }

    private function getDocumentUrl($document, $parentId, $path)
    {
        if (empty($document->pivot->file)) {
            return null;
        } else {
            return url('storage/app/' . $path . '/' . $parentId . '/expedient/' . $document->pivot->file);
        }
    }
}
