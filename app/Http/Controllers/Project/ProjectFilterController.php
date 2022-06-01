<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Process;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectFilterController extends Controller
{
    /**
     * @param Project $project
     * @param Process $process
     *
     * @return JsonResponse
     */
    public function getCurrentPhaseForm(Project $project, Process $process): JsonResponse
    {

        return $this->showList([]);
    }

    /**
     * @return JsonResponse
     */
    public function getMyProjects(): JsonResponse
    {

    }
}
