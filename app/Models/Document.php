<?php

/*
 * CODE
 * Document Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Validation\Rule;

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
     * Function to return array rules in method create and update
     *
     * @return array
     */
    public static function rules(): array
    {
        return [
            'name' => 'required|string',
            'description' => 'required|string',
            'quote' => 'required|int',
        ];
    }

    /**
     * @return BelongsToMany
     */
    public function client(): BelongsToMany
    {
        return $this->belongsToMany(Client::class);
    }

    /**
     * @return BelongsToMany
     */
    public function clientLink(): BelongsToMany
    {
        return $this->belongsToMany(ClientLink::class);
    }

    /**
     * @param $query
     * @param $search
     *
     * @return mixed
     */
    public function scopeSearch($query, $search): mixed
    {
        return $query->orWhere('name', 'like', "%$search%")
            ->orWhere('description', 'like', "%$search%")
            ->orWhere('quote', 'like', "%$search%");
    }
}
