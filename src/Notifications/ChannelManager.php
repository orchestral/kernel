<?php

namespace Orchestra\Notifications;

use Illuminate\Notifications\Bus;
use Illuminate\Notifications\Events\NotificationSent;
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
     * Send the given notification immtediately.
     *
     * @param  \Illuminate\Support\Collection|array  $notifiables
     * @param  mixed  $notification
     * @return void
     */
    public function sendNow($notifiables, $notification)
    {
        $config = $this->app->make('config');
        $events = $this->app->make('events');

        $notification->message();

        if (! $notification->application) {
            $notification->application($config->get('app.name'), $config->get('app.logo'));
        }

        if (property_exists($notification, 'title')) {
            $notification->subject("[{$notification->application}] {$notification->title}");
        }

        foreach ($notifiables as $notifiable) {
            $channels = $notification->via($notifiable);

            if (empty($channels)) {
                continue;
            }

            $events->fire(new NotificationSent($notifiable, $notification));

            foreach ($channels as $channel) {
                $this->driver($channel)->send(collect([$notifiable]), $notification);
            }
        }
    }

    /**
     * Queue the given notification instances.
     *
     * @param  \Illuminate\Support\Collection|array  $notifiables
     * @param  mixed  $notification
     *
     * @return void
     */
    protected function queueNotification($notifiables, $notification)
    {
        $job = (new SendQueuedNotifications($notifiables, $notification))
                    ->onConnection($notification->connection)
                    ->onQueue($notification->queue)
                    ->delay($notification->delay);

        $this->app->make(Bus::class)->dispatch($job);
    }
}
