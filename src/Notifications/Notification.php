<?php

namespace Orchestra\Notifications;

use BadMethodCallException;
use Illuminate\Notifications\Notification as BaseNotification;

class Notification extends BaseNotification
{
    /**
     * Get an array representation of the notification.
     *
     * @param  mixed  $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        $message = $this->message($notifiable);

        return [
            'notifiable'  => $notifiable,
            'application' => $this->application,
            'logoUrl'     => $this->logoUrl,
            'level'       => $message->level,
            'title'       => $message->title,
            'subject'     => $message->subject,
            'introLines'  => $message->introLines,
            'outroLines'  => $message->outroLines,
            'actionText'  => $message->actionText,
            'actionUrl'   => $message->actionUrl,
            'options'     => $message->options,
        ];
    }
    /**
     * Dynamically pass calls to the message class.
     *
     * @param  string  $method
     * @param  array  $parameters
     *
     * @return \Illuminate\Notifications\Message
     */
    public function __call($method, $parameters)
    {
        if (! method_exists(Message::class, $method)) {
            throw new BadMethodCallException("Call to undefined method [{$method}].");
        }

        $message = (new Message())->{$method}(...$parameters);

        if ($method == 'title') {
            $message->subject(sprintf('[%s] %s', $this->application, $message->title));
        }

        return $message;
    }
}
