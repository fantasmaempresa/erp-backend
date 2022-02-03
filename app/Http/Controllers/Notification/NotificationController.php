<?php

/*
 * CODE
 * Notification Controller
*/

namespace App\Http\Controllers\Notification;

use Exception;
use App\Models\Notification;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class NotificationController extends ApiController
{

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->showList(Notification::paginate(env('number_paginate')));
    }

    /**
     * @param Notification $notification
     *
     * @return JsonResponse
     */
    public function show(Notification $notification): JsonResponse
    {
        return $this->showOne($notification);
    }

//    /**
//     * @param Request $request
//     *
//     * @return JsonResponse
//     *
//     * @throws ValidationException
//     */
//    public function store(Request $request): JsonResponse
//    {
//        $this->validate($request, Notification::rules());
//        $notification = Notification::create($request->all());
//
//        return $this->showOne($notification);
//    }

    /**
     * @param Request $request
     * @param mixed   $reference
     *
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function update(Request $request, mixed $reference): JsonResponse
    {
        if ($request->has('notifications')) {
            $notifications = $request->get('notifications');
            DB::beginTransaction();
            try {
                foreach ($notifications as $notification) {
                    $notificationGet = Notification::findOrFail($notification['id']);
                    $notificationGet->check = Notification::$CHECK;
                    $notificationGet->save();
                }

            } catch (ModelNotFoundException) {
                DB::rollBack();

                return $this->errorResponse('Record not found', 404);
            }
        }
        DB::commit();

        return $this->showMessage('Records update successful');
    }

//    /**
//     * @param Notification $notification
//     *
//     * @return JsonResponse
//     *
//     * @throws Exception
//     */
//    public function destroy(Notification $notification): JsonResponse
//    {
//        $notification->delete();
//
//        return $this->showMessage('Record deleted successfully');
//    }
}
