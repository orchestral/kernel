<?php

namespace Orchestra\Notifications\Channels;

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
        $view = data_get($notification, 'options.view', 'orchestra/foundation::emails.notification');

        foreach ($notifiables as $notifiable) {
            $data = $this->prepareNotificationData($notifiable, $notification);

            $message = Message::create($view, $data, $data['subject']);

            $this->sendNotification($notifiable, $message);
        }
    }

    /**
     * Prepare the data from the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     *
     * @return void
     */
    protected function prepareNotificationData($notifiable, Notification $notification)
    {
        $data = $notification->toArray($notifiable);

        $data['title']       = $notification->title;
        $data['options']     = data_get($notification, 'options', []);
        $data['actionColor'] = $data['level'];

        return $data;
    }
}
