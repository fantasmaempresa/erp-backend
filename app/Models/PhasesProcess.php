<?php

/*
 * CODE
 * PhasesProcess Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Validation\Rule;

/**
 * @access  public
 *
 * @version 1.0
 */
class PhasesProcess extends Model
{
    public static bool $notification = true;
    public static bool $noNotification = false;

//    public static bool $supervision = true;
//    public static bool $noSupervision = false;

    public static bool $payment = true;
    public static bool $noPayment = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $fillable = [
        'id',
        'name',
        'description',
        'form',
        'payments',
        'notification',
        'supervision',
    ];

    protected function setNameAttribute($value){
        $this->attributes['name'] = strtolower($value);
    }
    
    protected function getNameAttribute($value){
        return strtoupper($value);
    }

    protected function setDescriptionAttribute($value){
        $this->attributes['description'] = strtolower($value);
    }
    
    protected function getDescriptionAttribute($value){
        return strtoupper($value);
    }
    

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'form' => 'array',
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
            'form' => 'required|array',
            'payments' => 'nullable|bool',
            'notification' => 'nullable|bool',
//            'supervision' => 'nullable|bool',
        ];
    }

    /**
     * @return BelongsToMany
     */
    public function process(): BelongsToMany
    {
        return $this->belongsToMany(Process::class);
    }

    /**
     * @return BelongsToMany
     */
//    public function roles(): BelongsToMany
//    {
//        return $this->belongsToMany(Role::class);
//    }

}
