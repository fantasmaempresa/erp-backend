<?php

/*
 * OPEN 2 CODE
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @version1
 */
class Appendant extends Model
{
    public $timestamps = false;

    /**
     * @var string[]
     */
    protected $fillable = [
        'id',
        'begin',
        'end',
        'factor',
    ];
}
