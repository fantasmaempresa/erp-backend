<?php

/**
 * open2code first version
 */

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\ApiController;
use App\Models\CategoryOperation;
use App\Models\Operation;
use App\Models\Procedure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Operation Controller open2code
 */
class OperationController extends ApiController
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
            $response = $this->showList(Operation::search($request->get('search'))->with('categoryOperation')->orderBy('name')->paginate($paginate));
        } else {
            $response = $this->showList(Operation::with('categoryOperation')->orderBy('name')->paginate($paginate));
        }

        return $response;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $this->validate($request, Operation::rules());
        $operation = Operation::create($request->all());

        if ($request->has('documents')) {
            $configDocuments = [];
            foreach ($request->get('documents') as $document) {
                $configDocuments['documents_required'][] = ["id" => $document['id']];
            }
            $operation->config = $configDocuments;
            $operation->save();
        }

        return $this->showOne($operation);
    }

    /**
     * Display the specified resource.
     *
     * @param Operation $operation
     *
     * @return JsonResponse
     */
    public function show(Operation $operation): JsonResponse
    {
        $operation->categoryOperation;
        return $this->showOne($operation);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Operation $operation
     *
     * @return JsonResponse
     */
    public function update(Request $request, Operation $operation): JsonResponse
    {
        $this->validate($request, Operation::rules());

        $oldConfigDocuments = $operation->config['documents_required'] ?? [];
        $oldCategoryOperation = $operation->category_operation_id;

        $operation->fill($request->all());
        $operation->save();

        if ($request->has('documents')) {
            $configDocuments = [];
            foreach ($request->get('documents') as $document) {
                $configDocuments['documents_required'][] = ["id" => $document['id']];
            }
            $operation->config = $configDocuments;
            $operation->save();

            $procedures = Procedure::join('operation_procedure', 'procedures.id', 'operation_procedure.procedure_id')
                ->where('operation_procedure.operation_id', $operation->id)
                ->select('procedures.*')
                ->get();

            //VIEJOS DOCUMENTOS
            $docuemntsOld = [];
            foreach ($oldConfigDocuments as $document) {
                $docuemntsOld[] = $document['id'];
            }

            if (!is_null($oldCategoryOperation)) {
                $categoryOperation = CategoryOperation::find($oldCategoryOperation);
                if (!is_null($categoryOperation->config['documents'])) {
                    foreach ($categoryOperation->config['documents'] as $document) {
                        $docuemntsOld[] = $document['id'];
                    }
                }
            }

            //NUEVOS DOCUMENTOS
            $documentsNew = [];
            foreach ($configDocuments['documents_required'] as $document) {
                $documentsNew[] = $document['id'];
            }

            $categoryOperation = CategoryOperation::find($operation->category_operation_id);
            if (isset($categoryOperation->config['documents']) && !empty($categoryOperation->config['documents'])) {
                foreach ($categoryOperation->config['documents'] as $document) {
                    $documentsNew[] = $document['id'];
                }
            }

            foreach ($procedures as $procedure) {
                $documents = [];
                foreach ($procedure->documents->toArray() as $document) {
                    if(in_array($document['id'], $docuemntsOld)) {
                        if($document['pivot']['file'] != '') {
                            $documents[] = $document['id'];    
                        }
                    }else{
                        $documents[] = $document['id'];
                    }
                }

                $documents = array_merge($documents, $documentsNew);
                $documents = array_unique($documents);
                $procedure->documents()->sync($documents);
            }
        }

        return $this->showOne($operation);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Operation $operation
     *
     * @return JsonResponse
     */
    public function destroy(Operation $operation): JsonResponse
    {
        $operation->delete();

        return $this->showMessage('Se elimino con éxito');
    }
}
