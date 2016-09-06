<?php

namespace Orchestra\Notifications\Messages;

use Illuminate\Notifications\Messages\MailMessage as Message;

class MailMessage extends Message
{
    use TitleForSubject;

    /**
     * The view for the message.
     *
     * @var string
     */
    public $view = 'orchestra/foundation::emails.notification';

    /**
     * Get an array representation of the message.
     *
     * @return array
     */
    public function toArray()
    {
        $data = parent::toArray();

        $data['title'] = $this->title;

        return $data;
    }
}
