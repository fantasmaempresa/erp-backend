<?php

namespace App\Http\Controllers\Reminder;

use App\Http\Controllers\ApiController;
use App\Models\Reminder;

class ReminderActionController extends ApiController
{
    
    public function enable_disable_reminder(Reminder  $reminder)
    {
        if ($reminder->status == Reminder::$DISABLE)
        {
            $reminder->status = Reminder::$NO_NOTIFED;
        } else {
            $reminder->status = Reminder::$DISABLE;
        }    

        $reminder->save();

        return $this->showOne($reminder);
    }

}
