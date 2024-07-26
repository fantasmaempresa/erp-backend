<?php

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\ApiController;
use App\Models\Operation;
use Illuminate\Http\Request;

class OperationFilterController extends ApiController
{
    public function documentsOperation(Request $request)
    {
        $request->validate([
            'operations' => 'required|array',
            'operations.*.id' => 'required|exists:operations,id',
        ]);

        $ids = array_column($request->get('operations'), 'id');

        // Utilizar el método whereIn para buscar por múltiples IDs
        $operations = Operation::whereIn('id', $ids)->with('categoryOperation')->get();

        $uniqueDocuments = [];
        foreach ($operations as $operation) {
            // Documentos de nivel superior
            // dd($operation->config, $operation->config['documents_required']);

            if ($operation->config && $operation->config['documents_required']) {
                $uniqueDocuments = array_merge($uniqueDocuments, $operation->config['documents_required']);
            }

            // Documentos de categoryOperation
            if ($operation->categoryOperation && $operation->categoryOperation->config && $operation->categoryOperation->config['documents_required']) {
                $uniqueDocuments = array_merge($uniqueDocuments, $operation->categoryOperation->config['documents_required']);
            }
        }

        // Eliminar duplicados utilizando array_unique
        $uniqueDocuments = array_unique($uniqueDocuments, SORT_REGULAR);

        return $this->showList($uniqueDocuments);
    }
}
