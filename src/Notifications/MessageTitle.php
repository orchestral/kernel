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
     * @return $this
     */
    public function title(string $title)
    {
        $this->title = $title;

        return $this;
    }
}
