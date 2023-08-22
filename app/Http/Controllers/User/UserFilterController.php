<?php
/*
 * CODE
 * User Filter Controller
*/

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * @access  public
 *
 * @version 1.0
 */
class UserFilterController extends ApiController
{
    /**
     * @return JsonResponse
     */
    public function getUsersOnline(): JsonResponse
    {
        return $this->showList(User::where('online', User::$ONLINE)->get());
    }

    /**
     * @return JsonResponse
     */
    public function getUsersOffline(): JsonResponse
    {
        return $this->showList(User::where('online', User::$OFFLINE)->get());
    }


}
