<?php

/*
 * CODE
 * Clients Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @access  public
 *
 * @version 1.0
 */
class Client extends Model
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
        'address',
        'rfc',
        'extra_information',
        'user_id',
    ];

    /**
     * @return belongsTo
     */
    public function user(): belongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany
     */
    public function project(): HasMany
    {
        return $this->HasMany(Project::class);
    }

    /**
     * @return BelongsToMany
     */
    public function clientDocument(): BelongsToMany
    {
        return $this->belongsToMany(Document::class);
    }
}
