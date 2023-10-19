<?php

/*
 * CODE
 * NationalConsumerPriceIndex Model Class
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * v1
 */
class NationalConsumerPriceIndex extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'year',
        'calendar',
    ];
    /**
     * @var array
     */
    protected $casts = [
        'calendar' => 'array',
    ];
}
