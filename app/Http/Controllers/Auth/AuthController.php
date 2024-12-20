<?php
/*
 * CODE
 * Auth Controller
*/

namespace App\Http\Controllers\Auth;

use App\Events\NotificationEvent;
use App\Events\RefreshDataEvent;
use App\Models\Role;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use App\Traits\NotificationTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Nyholm\Psr7\Response as Psr7Response;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @access  public
 *
 * @version 1.0
 */
class AuthController extends AccessTokenController
{
    use ApiResponseTrait, NotificationTrait;

    /**
     * @param ServerRequestInterface $request
     *
     * @return mixed
     *
     * @throws \Laravel\Passport\Exceptions\OAuthServerException
     */
    public function issueToken(ServerRequestInterface $request): mixed
    {
        if ($request->getParsedBody()['grant_type'] === 'refresh_token') {
            return parent::issueToken($request);
        }
        $user = User::where('email', '=', $request->getParsedBody()['username'])->first();

        if (empty($user) || !(Hash::check($request->getParsedBody()['password'], $user->password))) {
            return $this->errorResponse('invalid credentials', 401);
        }

        if ($user->locked === User::$LOCKED) {
            return $this->errorResponse('user locked', 401);
        }

        $user->role;
        $user->client;
        $user->staff;


        return $this->withErrorHandling(function () use ($request, $user) {
            $content = $this->convertResponse($this->server->respondToAccessTokenRequest($request, new Psr7Response()));
            $content = json_decode($content->getContent());
            $user->online = User::$ONLINE;
            $user->save();
            $content->user = $user;
            $content->user->url = null;
            if (isset($user->config['profile']['image'])) {
                $content->user->url = url('storage/app/users/profile/' . $user->config['profile']['image']);
            }

            $notification = $this->createNotification(User::getMessageNotify(User::$ONLINE, $user->name), null, Role::$ADMIN);

            $this->sendNotification(
                $notification,
                null,
                new NotificationEvent($notification, 0, Role::$ADMIN, [])
            );

            event(new RefreshDataEvent($user));

            return $content;
        });
    }

    /**
     * @return JsonResponse
     */
    public function logoutApi(): JsonResponse
    {

        if (Auth::check()) {
            Auth::user()->AauthAcessToken()->delete();
            Auth::user()->online = User::$OFFLINE;
            Auth::user()->save();

            $notification = $this->createNotification(User::getMessageNotify(User::$OFFLINE, Auth::user()->name), null, Role::$ADMIN);

            $this->sendNotification(
                $notification,
                null,
                new NotificationEvent($notification, 0, Role::$ADMIN, [])
            );
            $user = User::findOrFail(Auth::user()->id);
            $user->role;

            event(new RefreshDataEvent($user));

            return $this->successResponse('user logout!', 200);
        }

        return $this->errorResponse('error', 404);
    }

    /**
     * @return JsonResponse
     */
    public function onlineUser(): JsonResponse
    {
        Auth::user()->online = User::$ONLINE;
        Auth::user()->save();
        $user = User::findOrFail(Auth::user()->id);
        $user->role;

        event(new RefreshDataEvent($user));

        return $this->successResponse('user online', 200);
    }

    /**
     * @return JsonResponse
     */
    public function offlineUser(): JsonResponse
    {
        Auth::user()->online = User::$OFFLINE;
        Auth::user()->save();
        $user = User::findOrFail(Auth::user()->id);
        $user->role;
        event(new RefreshDataEvent($user));

        return $this->successResponse('user online', 200);
    }

    /**
     * @param User $user
     *
     * @return JsonResponse
     */
    public function lockUser(User $user): JsonResponse
    {
        $user->AauthAcessToken()->delete();
        $user->locked = User::$LOCKED;
        $user->save();
        $user->role;

        event(new RefreshDataEvent($user));

        return $this->successResponse('user locked!', 200);
    }

    /**
     * @param User $user
     *
     * @return JsonResponse
     */
    public function unlockUser(User $user): JsonResponse
    {
        $user->locked = User::$UNLOCKED;
        $user->save();
        $user->role;
        event(new RefreshDataEvent($user));

        return $this->successResponse('user unlocked!', 200);
    }
}
