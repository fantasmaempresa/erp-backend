<?php

/*
 * CODE
 * DetailProjectProcessProject Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @access  public
 *
 * @version 1.0
 */
class DetailProjectProcessProject extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'detail_project_id',
        'process_project_id',
    ];

    /**
     * @return BelongsTo
     */
    public function detailProject() : BelongsTo
    {
        return $this->belongsTo(DetailProject::class);
    }

    /**
     * @return BelongsTo
     */
    public function processProject() : BelongsTo
    {
        return $this->belongsTo(ProcessProject::class);
    }
}