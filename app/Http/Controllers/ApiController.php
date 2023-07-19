<?php

/*
 * Copyright CODE (2021)
 * Controller ApiController
 */

namespace App\Http\Controllers;

use App\Traits\ApiResponseTrait;
use App\Traits\NotificationTrait;
use http\Env\Request;
use Illuminate\Database\Eloquent\Model;

/**
 * @access  public
 *
 * @version 1.0
 */
class ApiController extends Controller
{
    use ApiResponseTrait, NotificationTrait;

}
