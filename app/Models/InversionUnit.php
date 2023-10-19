<?php

/*
 * OPEN 2 CODE
 * UDIS MODEL
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
