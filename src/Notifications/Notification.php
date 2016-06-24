<?php

namespace Orchestra\Notifications;

use Illuminate\Support\Str;
use Illuminate\Notifications\Notification as BaseNotification;

class Notification extends BaseNotification
{
    /**
     * Get the subject of the notification.
     *
     * @return string
     */
    public function title()
    {
        return property_exists($this, 'title')
                        ? $this->title
                        : Str::title(Str::snake(class_basename($this), ' '));
    }

    /**
     * Get the notification channel payload data.
     *
     * @return array
     */
    public function payload()
    {
        return [];
    }
}
