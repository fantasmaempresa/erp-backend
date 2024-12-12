<?php

/*
 * CODE
 * PhasesProcess Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


/**
 * @access  public
 *
 * @version 1.0
 */
class PhasesProcess extends Model
{

    /* */
    public static bool $notification = true;
    public static bool $noNotification = false;

    public static bool $payment = true;
    public static bool $noPayment = false;

    public static int $TYPE_PHASE_CREATE_FORM = 1;
    public static int $TYPE_PHASE_PREDEFINED_FORM = 2;
    public static int $TYPE_PHASE_PREDEFINED_FORM_WITH_FORMART = 3;

    /**
     * 
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $fillable = [
        'id',
        'name',
        'description',
        'form',
        'type_form',
        'payments',
        'notification',
        'supervision',
        'withFormat',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'form' => 'array',
        'withFormat' => 'array',
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
            'description' => 'nullable|string',
            'form' => 'required|array',
            'payments' => 'nullable|bool',
            'notification' => 'nullable|bool',
            'type_form' => 'required|int|min:1|max:3',
            'withFormat' => 'required_if:type_form,=,3|array',
        ];
    }

    /**
     * @return BelongsToMany
     */
    public function process(): BelongsToMany
    {
        return $this->belongsToMany(Process::class);
    }
}