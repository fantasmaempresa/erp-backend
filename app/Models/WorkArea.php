<?php

/*
 * CODE
 * WorkArea Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\Rule;

/**
 * @access  public
 *
 * @version 1.0
 */
class WorkArea extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable
        = [
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
    protected $casts
        = [
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
            'name'        => 'required|string',
            'description' => 'required|string',
<<<<<<< HEAD
            'config' => 'nullable|array',
=======
            'config'      => 'nullable|required|array',
>>>>>>> e58e357462885d55bb7fc8bf9ce0322e6a751ae0
        ];
    }

    /**
     * @return HasMany
     */
    public function staff(): HasMany
    {
        return $this->hasMany(Staff::class);
    }
}
