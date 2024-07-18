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
class
Document extends Model
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
        'config',
    ];

    protected $casts = [
        'config' => 'array',
    ];
    protected function setNameAttribute($value)
    {
        $this->attributes['name'] = strtolower($value);
    }

    protected function getNameAttribute($value)
    {
        return strtoupper($value);
    }

    protected function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = strtolower($value);
    }

    protected function getDescriptionAttribute($value)
    {
        return strtoupper($value);
    }

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
        return $this->belongsToMany(Client::class)->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function procedures(): BelongsToMany
    {
        return $this->belongsToMany(Procedure::class)->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function clientLink(): BelongsToMany
    {
        return $this->belongsToMany(ClientLink::class)->withTimestamps();
    }

    public function vulnerableOptions()
    {
        return $this->hasMany(VulnerableOperation::class);
    }

    public function books()
    {
        return $this->hasMany(Book::class);
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
