<?php

/*
 * OPEN2CODE PROCEDURE COMMENT CONTROLLER
 */

namespace App\Http\Controllers\ProcedureComment;

use App\Http\Controllers\ApiController;
use App\Models\ProcedureComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * version
 */
class ProcedureCommentController extends ApiController
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $this->validate($request, [
            'procedure_id' => 'required|exists:procedures,id',
        ]);

        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');
        
        if (!empty($request->get('search')) && $request->get('search') !== 'null') {
            $response = $this->showList(
                ProcedureComment::search($request->get('search'), $request->get('procedure_id'))
                ->with(['procedure', 'user'])->paginate($paginate)
            );
        } else {
            $response = $this->showList(
                ProcedureComment::where('procedure_id', $request->get('procedure_id'))
                ->with(['procedure', 'user'])
                ->paginate($paginate)
            );
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
        $this->validate($request, ProcedureComment::rules());
        $procedureComment = new ProcedureComment($request->all());
        $procedureComment->user_id = Auth::id();
        $procedureComment->save();
        $procedureComment->notify();

        return $this->showOne($procedureComment);
    }

    /**
     * @param ProcedureComment $procedureComment
     *
     * @return JsonResponse
     */
    public function show(ProcedureComment $procedureComment): JsonResponse
    {
        $procedureComment->procedure;
        $procedureComment->user;

        return $this->showOne($procedureComment);
    }

    /**
     * @param Request          $request
     * @param ProcedureComment $procedureComment
     *
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function update(Request $request, ProcedureComment $procedureComment): JsonResponse
    {
        $this->validate($request, ProcedureComment::rules());
        $procedureComment->fill($request->all());

        if ($procedureComment->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $procedureComment->save();

        return $this->showOne($procedureComment);
    }

    /**
     * @param ProcedureComment $procedureComment
     *
     * @return JsonResponse
     */
    public function destroy(ProcedureComment $procedureComment): JsonResponse
    {
        $procedureComment->delete();

        return $this->showMessage('Se elimino con éxito');
    }
}
