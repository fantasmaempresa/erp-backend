<?php

/*
 * CODE
 * DetailProject Model Class
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
class DetailProject extends Model
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
        'comments',
        'form_data',
        'phases_process_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'form_data' => 'array',
    ];


    /**
     * @return BelongsTo
     */
    public function phase(): BelongsTo
    {
        return $this->belongsTo(PhasesProcess::class);
    }

    /**
     * @return BelongsToMany
     */
    public function processProject(): BelongsToMany
    {
        return $this->belongsToMany(ProcessProject::class);
    }
}
