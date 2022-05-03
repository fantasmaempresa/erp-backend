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
     * @param array $config
     *
     * @return array|bool
     */
    public function verifyConfig(array $config): array|bool
    {
        return match (true) {
            empty($config['order_phases']) => ['error' => true, 'message' => 'not found order_phases'],
            !empty($config['order_phases']) => $this->verifyOrderPhases($config['order_phases']),
            default => false

        };
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
            if (!(isset($phase['phase']['id']) || isset($phase['next']) || isset($phase['previous']) || isset($phase['end_process']))) {
                return ['error' => true, 'message' => 'order_phases: error in the structure'];
            } else {
                $check = PhasesProcess::findOrFail($phase['phase']['id']);
                if (isset($phase['next']['phase']['id'])) {
                    $check = PhasesProcess::findOrFail($phase['next']['phase']['id']);
                }

                if (isset($phase['previous']['phase']['id'])) {
                    $check = PhasesProcess::findOrFail($phase['previous']['phase']['id']);
                }

                if (!isset($phase['order'])) {
                    return ['error' => true, 'message' => 'order_phases: order not found'];
                }


                $orderPhase[] = $phase['order'];
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


}
