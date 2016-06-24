<?php

namespace Orchestra\Notifications;

use Illuminate\Notifications\ChannelManager as Manager;

class ChannelManager extends Manager
{
    /**
     * Create an instance of the mail driver.
     *
     * @return \Illuminate\Notifications\Channels\MailChannel
     */
    protected function createMailDriver()
    {
        return $this->app->make(Channels\MailChannel::class);
    }

    /**
     * Build a new channel notification from the given object.
     *
     * @param  mixed  $notifiable
     * @param  mixed  $notification
     * @param  array|null  $channels
     *
     * @return array
     */
    public function notificationsFromInstance($notifiable, $notification, $channels = null)
    {
        return Channels\Notification::notificationsFromInstance($notifiable, $notification, $channels);
    }
}
