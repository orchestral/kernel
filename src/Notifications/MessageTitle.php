<?php

namespace Orchestra\Notifications;

trait MessageTitle
{
    /**
     * The title of the notification.
     *
     * @var string
     */
    public $title;

    /**
     * Get the title of the notification.
     *
     * @param  string  $title
     *
     * @return string
     */
    public function title($title)
    {
        $this->title = $title;

        return $this;
    }
}
