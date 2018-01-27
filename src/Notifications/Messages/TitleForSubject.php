<?php

namespace Orchestra\Notifications\Messages;

trait TitleForSubject
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
    public function title(string $title): self
    {
        $name = config('app.name');

        if (empty($this->subject) && ! is_null($name)) {
            $this->subject(sprintf('[%s] %s', $name, $title));
        }

        $this->title = $title;

        return $this;
    }
}
