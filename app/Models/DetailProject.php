<?php

/*
 * CODE
 * DetailProject Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use JetBrains\PhpStorm\ArrayShape;

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
    public static int $UNFINISHED = 0;
    public static int $FINISHED = 1;
    public static int $CURRENT = 2;


    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'comments',
        'form_data',
        'finished',
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
     * @return string[]
     */
    #[ArrayShape([
        'comments' => "string",
        'form_data' => "string",
        'phases_process_id' => "string",
    ])] public static function rules(): array
    {
        return [
            'comments' => 'required|string',
            'form_data' => 'required|array',
            'phases_process_id' => 'required|int',
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
     * @return BelongsTo
     */
    public function phase(): BelongsTo
    {
        return $this->belongsTo(PhasesProcess::class, 'phases_process_id');
    }

    /**
     * @return BelongsToMany
     */
    public function processProject(): BelongsToMany
    {
        return $this->belongsToMany(ProcessProject::class);
    }

}
