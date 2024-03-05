<?php

/*
 * OPEN 2 CODE
 * UDIS MODEL
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

/**
 * @access  public
 *
 * @version 1.0
 */
class InversionUnit extends Model
{
    public $timestamps = false;

    /**
     * @var string[]
     */
    protected $fillable = [
      'id',
      'date',
      'factor',
    ];

    public static function rules($id = null){
      $rules = [
        'date' => 'required',
        'factor' => 'required',
      ];

      if($id){
        $rules['date'] = ['required',Rule::unique('inversion_units')->ignore($id)];
      }

      return $rules;
    }
}
