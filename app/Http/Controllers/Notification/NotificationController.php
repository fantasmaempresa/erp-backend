<?php

/*
 * CODE
 * Notification Controller
*/

namespace App\Http\Controllers\Notification;

use Exception;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
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

//    /**
//     * @param Request $request
//     * @param Notification $notification
//     *
//     * @return JsonResponse
//     *
//     * @throws ValidationException
//     */
//    public function update(Request $request, Notification $notification): JsonResponse
//    {
//        $this->validate($request, Notification::rules());
//        $notification->fill($request->all());
//        if ($notification->isClean()) {
//            return $this->errorResponse('A different value must be specified to update', 422);
//        }
//
//        $notification->save();
//
//        return $this->showOne($notification);
//    }

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
