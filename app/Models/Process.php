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
    private array $phases = [];

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
        if (!isset($config['order_phases']) || !isset($config['involved'])) {
            return ['error' => true, 'message' => 'not found involved or order_phases'];
        }

        if ($this->verifyOrderPhases($config['order_phases'])) {
            return $this->verifyOrderPhases($config['order_phases']);
        }

        if ($this->verifyInvolved($config['involved'])) {
            return $this->verifyInvolved($config['involved']);
        }

        return false;
    }

    /**
     * @param array $involved
     *
     * @return array|bool
     */
    public function verifyInvolved(array $involved): array|bool
    {
        foreach ($involved as $field) {
            if (!isset($field['phase']) ||
                !isset($this->phases[$field['phase']['id']]) ||
                !isset($field['supervisor']) ||
                !isset($field['work_group'])
            ) {
                return ['error' => true, 'message' => 'involved: error in structure'];
            }

            foreach ($field['supervisor'] as $role) {
                $check = Role::findOrFail($role['id']);
            }

            foreach ($field['work_group'] as $role) {
                $check = Role::findOrFail($role['id']);
            }
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
        foreach ($orderPhases as $phase) {
            if (!(isset($phase['phase']['id']) || isset($phase['previous']) || isset($phase['end_process']) || isset($phase['order']))) {
                return ['error' => true, 'message' => 'order_phases: error in the structure'];
            } else {
                $check = PhasesProcess::findOrFail($phase['phase']['id']);
                $this->phases[$check->id] = $check->id;
                if (!isset($phase['order'])) {
                    return ['error' => true, 'message' => 'order_phases: order not found'];
                }
                $orderPhase[] = $phase['order'];

                if ($phase['order'] > 1) {
                    if (isset($phase['previous']['phase']['id'])) {
                        $check = PhasesProcess::findOrFail($phase['previous']['phase']['id']);
                        //TODO verificar si la phase es anterior.
                    }
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
        }

        foreach ($config['involved'] as $roles) {
            foreach ($roles['supervisor'] as $role) {
                $phasesAndRoles['roles'][$role['id']] = $role['id'];
            }

            foreach ($roles['work_group'] as $role) {
                $phasesAndRoles['roles'][$role['id']] = $role['id'];
            }
        }

        return $phasesAndRoles;
    }
}
