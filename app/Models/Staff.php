<?php

/*
 * CODE
 * Staff Model Class
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
class Staff extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'phone',
        'nickname',
        'extra_information',
        'work_area_id',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'extra_information' => 'array',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function workArea(): BelongsTo
    {
        return $this->belongsTo(WorkArea::class);
    }

    /**
     * @return BelongsToMany
     */
    public function project(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }
}
