<?php

namespace iProtek\Pay\Helpers;

use DB; 
use Illuminate\Support\Facades\Queue;

class NotificationHelper
{
    
    public static function send($notifiable, $notification)
    { 
        Queue::push(new \iProtek\Pay\NotificationQueue\NotificationJob($notifiable, $notification));
    }

}
