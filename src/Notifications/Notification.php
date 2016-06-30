<?php

namespace Orchestra\Notifications;

use Illuminate\Support\Str;
use Illuminate\Notifications\Notification as BaseNotification;

class Notification extends BaseNotification
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

    /**
     * Get the subject of the notification.
     *
     * @return string
     */
    public function subject()
    {
        return property_exists($this, 'subject')
                        ? $this->subject
                        : $this->title();
    }

    /**
     * Get the notification's options.
     *
     * @return array
     */
    public function options()
    {
        return property_exists($this, 'options') ? $this->options : [];
    }
}
