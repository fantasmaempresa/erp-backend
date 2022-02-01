<?php
/*
 * CODE
 * Auth Controller
*/

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
    /**
     * @param ServerRequestInterface $request
     *
     * @return Response
     *
     * @throws \Laravel\Passport\Exceptions\OAuthServerException
     */
    public function issueToken(ServerRequestInterface $request)
    {
        if($request->getParsedBody()['grant_type'] == 'refresh_token' ){
            return parent::issueToken($request);
        }
        $user = User::where('email', '=', $request->getParsedBody()['username'])->first();

        if (empty($user) || !(Hash::check($request->getParsedBody()['password'], $user->password))) {
            return $this->errorResponse('invalid credentials', 401);
        }

        $user->role;
        $user->client;
        $user->staff;

        return $this->withErrorHandling(function () use ($request, $user) {
            $content =  $this->convertResponse($this->server->respondToAccessTokenRequest($request, new Psr7Response()));
            $content = json_decode($content->getContent());
            $content->user = $user;

            return $content;
        });
    }
}
