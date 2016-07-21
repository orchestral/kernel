<?php

namespace Orchestra\Notifications;

use Illuminate\Notifications\Notification as BaseNotification;

class Notification extends BaseNotification
{
    use NotificationTitle;

    /**
     * Get the subject of the notification.
     *
     * @return string
     */
    public function subject()
    {
        return property_exists($this, 'subject') ? $this->subject : $this->title();
    }
}
