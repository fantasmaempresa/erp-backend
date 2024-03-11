<?php

/*
 * CODE
 * TemplateQuotes Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @access  public
 *
 * @version 1.0
 */
class TemplateQuotes extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'form',
        'operations',
    ];

    protected function setNameAttribute($value){
        $this->attributes['name'] = strtolower($value);
    }
    
    protected function getNameAttribute($value){
        return strtoupper($value);
    }


    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'form' => 'array',
        'operations' => 'array',
    ];

    /**
     * Function to return array rules in method create and update
     *
     * @return array
     */
    public static function rules(): array
    {
        return [
            'name' => 'required|string|max:250',
            'form' => 'required|array',
            'operations' => 'nullable|array',
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
        return $query->where('name', 'like', "%$search%");
    }

    /**
     * @return BelongsTo
     */
    public function templateQuote(): BelongsTo
    {
        return $this->belongsTo(ProjectQuote::class);
    }
}
