<?php

namespace Orchestra\Notifications;

use Illuminate\Support\Str;

trait NotificationTitle
{
    /**
     * Get the title of the notification.
     *
     * @return string
     */
    public function title()
    {
        return property_exists($this, 'title')
                        ? $this->title
                        : Str::title(Str::snake(class_basename($this), ' '));
    }
}
