<?php

/*
 * OPEN2CODE
 * TypeDisposalOperation Model
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @version1
 */
class TypeDisposalOperation extends Model
{
    public $timestamps = false;

    /**
     * @var string[]
     */
    protected $fillable = [
      'id',
      'type',
    ];
}
