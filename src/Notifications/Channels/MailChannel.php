<?php

namespace Orchestra\Notifications\Channels;

use Illuminate\Support\Arr;
use Orchestra\Notifier\Message;
use Orchestra\Notifier\Notifiable;
use Orchestra\Notifications\Notification;

class MailChannel
{
    use Notifiable;

    /**
     * Send the given notification.
     *
     * @param  \Illuminate\Support\Collection  $notifiables
     * @param  \Illuminate\Notifications\Notification  $notification
     *
     * @return void
     */
    public function send($notifiables, Notification $notification)
    {
        if ($notifiables->isEmpty()) {
            return;
        }

        $message = Message::create(
            data_get($notification, 'options.view', 'orchestra/foundation::emails.notification'),
            $this->prepareNotificationData($notification),
            $notification->subject
        );

        $this->sendNotifications($notifiables, $message);
    }

    /**
     * Prepare the data from the given notification.
     *
     * @param  \Illuminate\Notifications\Channels\Notification  $notification
     *
     * @return void
     */
    protected function prepareNotificationData($notification)
    {
        $data = $notification->toArray();

        $data['title']       = $notification->title;
        $data['options']     = data_get($notification, 'options', []);
        $data['actionColor'] = $data['level'];

        return $data;
    }
}
