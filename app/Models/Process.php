<?php

/*
 * CODE
 * Process Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @access  public
 *
 * @version 1.0
 */
class Process extends Model
{
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
    public static function rules(): array
    {
        return [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'config' => 'nullable|array',
        ];
    }

    /**
     * @return HasMany
     */
    public function phases(): HasMany
    {
        return $this->hasMany(PhasesProcess::class);
    }

    /**
     * @return BelongsToMany
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }


}
