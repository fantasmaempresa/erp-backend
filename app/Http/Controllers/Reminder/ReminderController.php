<?php

namespace App\Http\Controllers\Reminder;

use App\Http\Controllers\ApiController;
use App\Models\Reminder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ReminderController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): JsonResponse
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        if (!empty($request->get('search')) && $request->get('search') !== 'null') {
            $sql = Reminder::search($request->get('search'))->with('user');
        }
        else if (!empty($request->get('view')) && $request->get('view') === 'Procedures') {
            $sql = Reminder::where('type', Reminder::$PROCEDURE_CONFIG);
            if(!empty($request->get('client_id')) && $request->get('client_id') !== 'null'){
                $sql = $sql->where('relation_id', $request->get('client_id'));
            }            
        }
        else if (!empty($request->get('view')) && $request->get('view') === 'ProcessingIncome') {
            $sql = Reminder::where('type', Reminder::$PROCESSING_INCOME_CONFIG);
            if(!empty($request->get('client_id')) && $request->get('client_id') !== 'null'){
                $sql = $sql->where('relation_id', $request->get('client_id'));
            }            
        }
        else
            $sql = Reminder::with('user');

        return $this->showList($sql->orderBy('id', 'desc')->paginate($paginate));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        $this->validate($request, Reminder::rules());

        $config = Reminder::getConfig($request->get('type'));

        if (empty($config)) {
            return $this->errorResponse('La configuración no es correcta', 422);
        }

        $this->validate($request, $config);
        $reminder = new Reminder($request->all());

        if ($reminder->type == Reminder::$PROCESSING_INCOME_CONFIG) {
            $reminder->relation_id = $request->get('processing_income_id');
        }else if ($reminder->type == Reminder::$PROCEDURE_CONFIG){
            $reminder->relation_id = $request->get('procedure_id');
        }

        $reminder->user_id = Auth::user()->id;
        $reminder->status = Reminder::$NO_NOTIFED;
        $reminder->save();

        return $this->showOne($reminder);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Reminder  $reminder
     * @return \Illuminate\Http\Response
     */
    public function show(Reminder $reminder): JsonResponse
    {
        return $this->showOne($reminder);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Reminder  $reminder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Reminder $reminder)
    {
        $this->validate($request, Reminder::rules());

        $config = Reminder::getConfig($request->get('type'));

        if (empty($config)) {
            return $this->errorResponse('La configuración no es correcta', 422);
        }

        $reminder->fill($request->all());
        if ($reminder->type == Reminder::$PROCESSING_INCOME_CONFIG) {
            $reminder->relation_id = $request->get('processing_income_id');
        }else if ($reminder->type == Reminder::$PROCEDURE_CONFIG){
            $reminder->relation_id = $request->get('procedure_id');
        }


        $reminder->user_id = Auth::user()->id;
        $reminder->save();

        return $this->showOne($reminder);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Reminder  $reminder
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reminder $reminder): JsonResponse
    {
        $reminder->delete();
        return $this->showMessage('Record deleted successfully');
    }
}
