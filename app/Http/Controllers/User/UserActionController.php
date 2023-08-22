<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserActionController extends ApiController
{

    public function updateMyInfoUser(Request $request): JsonResponse
    {
        $this->validate($request, [
            'id' => 'required|int',
            'name' => 'required|string',
            'change_password' => 'nullable|string',
            'confirm_change_password' => 'nullable|string',
            'password' => 'required|string',
            'file' => 'nullable|sometimes|image|mimes:jpeg,bmp,png,jpg,svg|max:2000',
        ]);

        if (Auth::id() != $request->get('id')) {

            return $this->errorResponse('ids not equals', 422);
        }


        $user = User::findOrFail(Auth::id());


        if (!Hash::check($request->get('password'), $user->password)) {

            return $this->errorResponse('la contraseña no es correcta', 422);
        }


        $user->fill($request->only(['name']));
        if (!$user->isClean()) {
            $user->save();
        }

        if (
            !empty($request->get('change_password')) &&
            !empty($request->get('confirm_change_password')) &&
            $request->get('change_password') == $request->get('confirm_change_password')
        ) {
            $user->password = bcrypt($request->get('change_password'));
            $user->save();
        }

        $file = $request->file('file');

        if (!empty($file)) {
            DB::beginTransaction();
            try {
                $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('users/profile/', $fileName);
                $config = $user->config;
                $config['profile'] = ['image' => $fileName];
                $user->config = $config;
                $user->save();
            } catch (\Exception $e) {
                DB::rollBack();

                return $this->errorResponse('ocurrio un error al almacenar la imagen', 422);
            }
        }

        DB::commit();

        return $this->successResponse('update complete', 200);
    }
}
