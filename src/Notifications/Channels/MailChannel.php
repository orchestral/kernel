<?php

namespace Orchestra\Notifications\Channels;

use Illuminate\Support\Arr;
use Orchestra\Notifier\Message;
use Orchestra\Notifier\Notifiable;
use Illuminate\Notifications\Channels\Notification;

class MailChannel
{
    use Notifiable;

    /**
     * Send the given notification.
     *
     * @param  \Orchestra\Notifications\Channels\Notification  $notification
     *
     * @return void
     */
    public function send(Notification $notification)
    {
        $users = $notification->notifiables;

        if ($users->isEmpty()) {
            return;
        }

        $message = Message::create(
            data_get($notification, 'payload.view', 'orchestra/foundation::emails.notification'),
            $this->prepareNotificationData($notification),
            $notification->subject
        );

        $this->sendNotifications($users, $message);
    }

    /**
     * Prepare the data from the given notification.
     *
     * @param  \Illuminate\Notifications\Channels\Notification  $notification
     * @return void
     */
    protected function prepareNotificationData($notification)
    {
        $data = $notification->toArray();

        return Arr::set($data, 'actionColor', $data['level']);
    }
}
