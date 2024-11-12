<?php

/*
 * CODE
 * ProcessProject Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @access  public
 *
 * @version 1.0
 */
class ProcessProject extends Model
{


    protected $table = 'process_project';
    public $incrementing = true;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'project_id',
        'process_id',
        'status',
    ];

    /**
     * @return BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return BelongsTo
     */
    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }

    /**
     * @return BelongsToMany
     */
    public function detailProject(): BelongsToMany
    {
        return $this->belongsToMany(DetailProject::class);
    }
}
