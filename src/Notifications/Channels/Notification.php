<?php

namespace Orchestra\Notifications\Channels;

use Illuminate\Support\Collection;
use Orchestra\Notifications\ChannelManager;
use Illuminate\Notifications\Channels\Notification as BaseNotification;

class Notification extends BaseNotification
{
    /**
     * The title of the notification.
     *
     * @var string
     */
    public $title;

    /**
     * Create a new notification instance.
     *
     * @param array  $notifiables
     */
    public function __construct($notifiables)
    {
        $this->notifiables = Collection::make($notifiables);
        $this->application = memorize('site.name');
    }

    /**
     * Specify the application logo sending the notification.
     *
     * @param  string  $logoUrl
     *
     * @return $this
     */
    public function logo($logoUrl = null)
    {
        $this->logoUrl = $logoUrl;

        return $this;
    }

    /**
     * Set the title of the notification.
     *
     * @param  string  $title
     *
     * @return $this
     */
    public function title($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Build a new channel notification.
     *
     * @param  mixed  $notifiable
     * @param  mixed  $instance
     * @param  string  $channel
     *
     * @return static
     */
    protected static function buildNotification($notifiable, $instance, $channel)
    {
        $notification = parent::buildNotification($notifiable, $instance, $channel);

        if (method_exists($instance, 'title')) {
            $notification->title($instance->title());
            $notification->subject("[{$notification->application}] {$notification->subject}");
        }

        return $notification;
    }
}
