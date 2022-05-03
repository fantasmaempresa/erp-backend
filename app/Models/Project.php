<?php

/*
 * CODE
 * Projects Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'name',
        'description',
        'estimate_end_date',
        'quotes',
        'folio',
        'user_id',
        'project_quote_id',
        'client_id',
    ];
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
//    protected $casts = [
//        'quotes' => 'array',
//    ];

    /**
     * Function to return array rules in method create and update
     *
     * @return array
     */
    public static function rules(): array
    {
        return [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'estimate_end_date' => 'nullable|date',
            'quotes' => 'nullable|array',
            'folio' => 'nullable|string',
            'project_quote_id' => 'nullable|int',
//            'user_id' => 'required|int',
            'client_id' => 'nullable|int',
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
        return $this->belongsToMany(Process::class)->withPivot('id');
    }

    /**
     * @return BelongsToMany
     */
    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class);
    }

    /**
     * @return HasManyThrough
     */
    public function processProject(): HasManyThrough
    {
        return $this->hasManyThrough(ProcessProject::class, DetailProjectProcessProject::class);
    }
}
