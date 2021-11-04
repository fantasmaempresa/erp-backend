<?php

/*
 * CODE
 * Document Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @access  public
 *
 * @version 1.0
 */
class Document extends Model
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
        'quote',
    ];

    /**
     * @return BelongsToMany
     */
    public function client(): BelongsToMany
    {
        return $this->belongsToMany(Client::class);
    }
}
