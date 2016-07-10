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
     * Build a new channel notification from the given object.
     *
     * @param  mixed  $notifiable
     * @param  mixed  $instance
     * @param  array|null  $channels
     *
     * @return array[static]
     */
    public static function notificationsFromInstance($notifiable, $instance, $channels = null)
    {
        $notifications = [];

        $channels = $channels ?: $instance->via($notifiable);

        $channels = $channels ?: app(ChannelManager::class)->deliversVia();

        foreach ($channels as $channel) {
            $notifications[] = $notification = new static([$notifiable]);

            $notification->via($channel)
                         ->subject($instance->subject())
                         ->level($instance->level());

            $method = static::messageMethod($instance, $channel);

            foreach ($instance->{$method}($notifiable)->elements as $element) {
                $notification->with($element);
            }

            $method = static::optionsMethod($instance, $channel);

            $notification->options($instance->{$method}($notifiable));

            if (method_exists($instance, 'title')) {
                $notification->title($instance->title());
                $notification->subject("[{$notification->application}] {$notification->subject}");
            }
        }

        return $notifications;
    }
}
