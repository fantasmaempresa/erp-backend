<?php

/*
 * CODE
 * Process Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use JetBrains\PhpStorm\ArrayShape;
use function PHPUnit\Framework\isEmpty;
use function Psy\debug;

/**
 * @access  public
 *
 * @version 1.0
 */
class Process extends Model
{
    /**
     * @var int
     */
    public static int $FINISHED = 1;
    public static int $UNFINISHED = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'name',
        'description',
        'config',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'config' => 'array',
    ];

    /**
     * Function to return array rules in method create and update
     *
     * @return array
     */
    #[ArrayShape([
        'name' => "string",
        'description' => "string",
        'config' => "string",
    ])] public static function rules(): array
    {
        return [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'config' => 'nullable|array',
        ];
    }

    /**
     * @param $query
     * @param $search
     *
     * @return mixed
     */
    public function scopeSearch($query, $search): mixed
    {
        return $query->orWhere('name', 'like', "%$search%");
    }

    /**
     * @return BelongsToMany
     */
    public function phases(): BelongsToMany
    {
        return $this->belongsToMany(PhasesProcess::class);
    }

    /**
     * @return BelongsToMany
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }

    /**
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * @param array $config
     *
     * @return array|bool
     */
    public function verifyConfig(array $config): array|bool
    {
        if (!isset($config['order_phases'])) {
            return ['error' => true, 'message' => 'not found order_phases'];
        }

        if ($this->verifyOrderPhases($config['order_phases'])) {
            return $this->verifyOrderPhases($config['order_phases']);
        }

        return false;
    }

    /**
     * @param array $orderPhases
     *
     * @return array|bool
     */
    public function verifyOrderPhases(array $orderPhases): array|bool
    {
        $orderPhase = [];
        $idPhases = [];
        foreach ($orderPhases as $phase) {
            if (!(isset($phase['phase']['id']) || isset($phase['previous']) || isset($phase['end_process']) || isset($phase['order']))) {
                return ['error' => true, 'message' => 'order_phases: error in the structure'];
            } else {
                $check = PhasesProcess::findOrFail($phase['phase']['id']);
                $idPhases[$check->id] = $check->id;
                if (!isset($phase['order'])) {
                    return ['error' => true, 'message' => 'order_phases: order not found'];
                }
                $orderPhase[] = $phase['order'];

                if ($phase['order'] > 1) {
                    if (isset($phase['previous']['phase']['id'])) {
                        $check = PhasesProcess::findOrFail($phase['previous']['phase']['id']);
                        if (!isset($idPhases[$check->id])) {
                            return ['error' => true, 'message' => 'order_phases: previous phase not found in order phases'];
                        }
                    }
                }
                if (!isset($phase['involved']['supervisor']) ||
                    !isset($phase['involved']['work_group'])
                ) {
                    return ['error' => true, 'message' => 'involved: error in structure'];
                }

                foreach ($phase['involved']['supervisor'] as $role) {
                    $check = Role::findOrFail($role['id']);
                }

                foreach ($phase['involved']['work_group'] as $role) {
                    $check = Role::findOrFail($role['id']);
                }
            }
        }

        sort($orderPhase);
        $count = 1;
        foreach ($orderPhase as $phase) {
            if (!($phase === $count)) {
                return ['error' => true, 'message' => 'order_phases: he order of the phases is not correct'];
            }
            $count++;
        }

        return false;
    }

    /**
     * @param array $config
     *
     * @return array
     */
    public function getPhasesAndRoles(array $config): array
    {
        $phasesAndRoles = [];
        foreach ($config['order_phases'] as $phase) {
            $phasesAndRoles['phases'][$phase['phase']['id']] = $phase['phase']['id'];

            foreach ($phase['involved']['supervisor'] as $role) {
                $phasesAndRoles['roles'][$role['id']] = $role['id'];
            }

            foreach ($phase['involved']['work_group'] as $role) {
                $phasesAndRoles['roles'][$role['id']] = $role['id'];
            }
        }

        return $phasesAndRoles;
    }
}
