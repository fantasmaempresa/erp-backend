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
    'name',
    'factor',
  ];

  public static function rules($id = null)
  {
    $rules = [
      'name' => 'required|unique:inversion_units',
      'factor' => 'required',
    ];

    if ($id) {
      $rules['name'] = ['required', Rule::unique('inversion_units')->ignore($id)];
    }

    return $rules;
  }
}
