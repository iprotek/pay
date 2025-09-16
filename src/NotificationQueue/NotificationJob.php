<?php
namespace iProtek\Pay\NotificationQueue;

use App\Notifications\MyNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationJob implements ShouldQueue
{
    use InteractsWithQueue;

    protected $user;
    protected $notification;

    public function __construct($user, $notification)
    {
        $this->user = $user;
        $this->notification = $notification;
    }

    public function handle()
    {
        $this->user->notify($this->notification);
    }
}