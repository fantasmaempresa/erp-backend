<?php

/*
 * CODE
 * Process Controller
*/

namespace App\Http\Controllers\Process;

use Exception;
use App\Models\Process;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Validation\ValidationException;

/**
 * @access  public
 *
 * @version 1.0
 */
class ProcessController extends ApiController
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
            $response = $this->showList(Process::search($request->get('search')->with('phases')->orderBy('id','desc')->paginate($paginate)));
        } else {
            $response = $this->showList(Process::with('phases')->with('roles')->orderBy('id','desc')->paginate($paginate));
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
        $this->validate($request, Process::rules());
        $process = new Process($request->all());
        $validityConfig = $process->verifyConfig($request->get('config'));
        if ($validityConfig) {
            return $this->errorResponse($validityConfig, 409);
        }
        $process->save();
        $rolesAndPhases = $process->getPhasesAndRoles($request->get('config'));

        foreach ($rolesAndPhases['roles'] as $roleId) {
            $process->roles()->attach($roleId);
        }

        foreach ($rolesAndPhases['phases'] as $phaseId) {
            $process->phases()->attach($phaseId);
        }

        $process->roles;
        $process->phases;

        return $this->showOne($process);
    }

    /**
     * @param Process $process
     *
     * @return JsonResponse
     */
    public function show(Process $process): JsonResponse
    {
        $process->roles;
        $process->phases;

        return $this->showOne($process);
    }

    /**
     * @param Request $request
     * @param Process $process
     *
     * @return JsonResponse
     *
     * @throws ValidationException
     *
     */
    public function update(Request $request, Process $process): JsonResponse
    {
        $this->validate($request, Process::rules());
        $validityConfig = $process->verifyConfig($request->get('config'));
        if ($validityConfig) {
            return $this->errorResponse($validityConfig, 409);
        }
        $process->fill($request->all());
        if ($process->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $process->save();
        $rolesAndPhases = $process->getPhasesAndRoles($request->get('config'));
        $process->phases()->sync($rolesAndPhases['phases']);
        $process->roles()->sync($rolesAndPhases['roles']);
        $process->phases;
        $process->roles;

        return $this->showOne($process);
    }

    /**
     * @param Process $process
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Process $process): JsonResponse
    {
        $process->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
