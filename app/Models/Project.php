<?php

/*
 * CODE
 * Projects Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @access  public
 *
 * @version 1.0
 */
class Project extends Model
{
    /**
     * @var int
     */
    public static int $FINISHED = 1;
    public static int $UNFINISHED = 0;
    public static int $INPROGRESS = 2;
    public static int $NOSTART = 3;

    public static int $RELOAD_CURRENT_FORM_ACTION = 0;
    public static int $RELOAD_MY_PROJECT_ACTION = 1;
    public static int $SEND_NOTIFY_MY_PROJECT_ACTION = 2;

    //TYPE PROJECTS

    static int $PROJECT_PUBLIC_WRINTING = 1;
    static int $PROJECT_NOTARIAL_DEED = 2;

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
        'finished',
        'user_id',
        'staff_id',
        'client_id',
        'procedure_id',
        'project_quote_id',
        'type_project',
    ];

    protected function setNameAttribute($value)
    {
        $this->attributes['name'] = strtolower($value);
    }

    protected function getNameAttribute($value)
    {
        return strtoupper($value);
    }

    protected function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = strtolower($value);
    }

    protected function getDescriptionAttribute($value)
    {
        return strtoupper($value);
    }

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
    public static function rules(): array
    {
        return [
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'quotes' => 'nullable|array',
            'project_quote_id' => 'required|int',
            'config' => 'required|array',
            'staff_id' => 'required|int',
            'client_id' => 'nullable|int',
            'type_project' => 'required|int|min:1|max:2',
        ];
    }

    /**
     * @return BelongsTo
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return BelongsTo
     */
    public function procedure(): BelongsTo
    {
        return $this->belongsTo(Procedure::class);
    }

    /**
     * @return BelongsTo
     */
    public function projectQuote(): BelongsTo
    {
        return $this->belongsTo(ProjectQuote::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsToMany
     */
    public function process(): BelongsToMany
    {
        return $this->belongsToMany(Process::class)->withPivot(['id', 'status'])->with('phases');
    }

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * @return HasManyThrough
     */
    public function processProjectThrough(): HasManyThrough
    {
        return $this->hasManyThrough(DetailProjectProcessProject::class, ProcessProject::class)->with('detailProject');
    }

    /**
     * @return HasMany
     */
    public function processProject(): HasMany
    {
        //        return $this->hasManyThrough(DetailProjectProcessProject::class, ProcessProject::class);
        return $this->hasMany(ProcessProject::class);
    }

    public function staff(){    
        return $this->belongsTo(Staff::class);
    }


    /**
     * @param array $config
     *
     * @return array|bool
     */
    public function verifyConfig(array $config): array|bool
    {
        $error = false;
        foreach ($config as $field) {
            if (!isset($field['process']['id']) || !isset($field['phases'])) {
                $error = ['error' => true, 'message' => 'config: error in structure'];
            }

            $process = Process::findOrFail($field['process']['id']);
            $phaseProcess = $process->getPhasesAndRoles($process->config);


            foreach ($field['phases'] as $phase) {
                if (!isset($phaseProcess['phases'][$phase['phase']['id']])) {
                    $error = ['error' => true, 'message' => 'config: error, phase not fount in process ' . $phase['phase']['id']];
                    break 2;
                }

                foreach ($phase['involved']['supervisor'] as $supervisor) {
                    if (!$supervisor['user'] && !isset($phaseProcess['roles'][$supervisor['id']])) {
                        $error = ['error' => true, 'message' => 'config: error, role not fount in process ' . $supervisor['id']];
                        break 3;
                    }

                    if (!isset($supervisor['mandatory_supervision'])) {
                        $error = ['error' => true, 'message' => 'config: error in structure mandatory_supervision not fount'];
                        break 3;
                    }
                }

                foreach ($phase['involved']['work_group'] as $workGroup) {
                    if (!$workGroup['user'] && !isset($phaseProcess['roles'][$workGroup['id']])) {
                        $error = ['error' => true, 'message' => 'config: error, role not fount in process ' . $workGroup['id']];
                        break 3;
                    }

                    if (!isset($workGroup['mandatory_work'])) {
                        $error = ['error' => true, 'message' => 'config: error in structure mandatory_work not fount'];
                        break 3;
                    }
                }
            }
        }

        return $error;
    }

    /**
     * @param array $config
     *
     * @return array
     */
    public function getUsersAndProcess(array $config): array
    {
        $processUsers = [];

        foreach ($config as $field) {
            $processUsers['process'][$field['process']['id']] = $field['process']['id'];

            foreach ($field['phases'] as $phase) {
                foreach ($phase['involved']['supervisor'] as $supervisor) {
                    if ($supervisor['user']) {
                        $processUsers['users'][$supervisor['id']] = $supervisor['id'];
                    }
                }

                foreach ($phase['involved']['work_group'] as $workGroup) {
                    if ($workGroup['user']) {
                        $processUsers['users'][$workGroup['id']] = $workGroup['id'];
                    }
                }
            }
        }

        return $processUsers;
    }

    /**
     * @param int $action
     *
     * @return array
     */
    public static function getActionSystem(int $command, ?string $action): array
    {
        $commands = [
            self::$RELOAD_CURRENT_FORM_ACTION => [
                'command' => 'reload_current_form',
                'action' => $action,
            ],
            self::$RELOAD_MY_PROJECT_ACTION => [
                'command' =>'reload_my_project',
                'action' => $action,
            ],
            self::$SEND_NOTIFY_MY_PROJECT_ACTION => [
                'command' =>'send_internal_notification',
                'action' => $action,
            ],
            
        ];

        return $commands[$command] ?? [];
    }
}
