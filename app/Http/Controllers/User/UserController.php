<?php
/*
 * CODE
 * User Controller
*/

namespace App\Http\Controllers\User;

use App\Models\Client;
use App\Models\Staff;
use App\Models\User;
use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;

/**
 * @access  public
 *
 * @version 1.0
 */
class UserController extends ApiController
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        if ($request->has('search')) {
            $response = $this->showList(User::search($request->get('search'))->with('role')->paginate($paginate));
        } else {
            $response = $this->showList(User::with('role')->with('staff')->paginate($paginate));
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
        $this->validate($request, User::rules());
        $user = User::create($request->all());
        $user->password = bcrypt($user->password);
        $user->save();

        return $this->showOne($user);
    }

    /**
     * Display the specified resource.*
     * @param User $user
     *
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        return $this->showOne($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $user
     *
     * @return JsonResponse
     *
     * @throws ValidationException
     *
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $this->validate($request, User::rules($user->id));
        $user->fill($request->all());
        if ($user->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }
        $user->password = empty($request->has('password'))
            ? $user->password
            : bcrypt($request->has('password'));
        $user->save();

        return $this->showOne($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(User $user): JsonResponse
    {
        $user->client()->delete();
        $user->staff()->delete();
        $user->project()->delete();
        $user->notification()->delete();
        $user->AauthAcessToken()->delete();
        $user->log()->delete();
        $user->delete();

        return $this->showMessage('Record deleted successfully');
    }

    public function assignUserToEntity(Request $request)
    {
        $this->validate($request, [
            'view' => 'required|string',
            'user_id' => 'required|int',
            'entity_id' => 'required|int',
        ]);

        $user = User::findOrFail($request->get('user_id'));
        $user->staff;

        if(!empty($user->staff)){
            print_r($user->staff);
            return $this->errorResponse('this user as already have client or staff', 406);
        }




        if ($request->get('view') == 'staff') {
            $entity = Staff::findOrFail($request->get('entity_id'));


//        } elseif ($request->get('view') == 'client') {
//            $entity = Client::findOrFail($request->get('entity_id'));
//
        }
        else {
            return $this->errorResponse('not correct value view',406);
        }

        $entity->user_id = $user->id;
        $entity->save();


    }
}
