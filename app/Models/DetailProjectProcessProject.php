<?php

/*
 * CODE
 * DetailProjectProcessProject Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @access  public
 *
 * @version 1.0
 */
class DetailProjectProcessProject extends Model
{
    protected $table = 'detail_project_process_project';
    public $incrementing = true;
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
