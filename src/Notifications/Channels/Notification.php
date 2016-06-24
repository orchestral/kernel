<?php

namespace Orchestra\Notifications\Channels;

use Orchestra\Support\Str;
use Illuminate\Support\Collection;
use Orchestra\Notifications\ChannelManager;
use Illuminate\Notifications\MessageBuilder;
use Illuminate\Notifications\Channels\Notification as BaseNotification;

class Notification extends BaseNotification
{
    /**
     * The data payload of the notification.
     *
     * @var array
     */
    public $payload = [];

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
     * @return $this
     */
    public function title($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set the data payload of the notification.
     *
     * @param  array  $payload
     *
     * @return $this
     */
    public function payload(array $payload)
    {
        $this->payload = $payload;

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

            $payload = call_user_func([$instance, static::payloadMethod($instance, $channel))], $notifiable);

            if (is_array($payload)) {
                $notification->payload($payload);
            }

            $message = call_user_func([$instance, static::messageMethod($instance, $channel))], $notifiable);

            if ($message instanceof MessageBuilder) {
                foreach ($message->elements as $element) {
                    $notification->with($element);
                }
            }

            if (method_exists($instance, 'title')) {
                $notification->title($instance->title());
                $notification->subject("[{$notification->application}] {$notification->title}");
            }
        }

        return $notifications;
    }

    /**
     * Get the proper data method for the given instance and channel.
     *
     * @param  mixed  $instance
     * @param  string  $channel
     *
     * @return string
     */
    protected static function payloadMethod($instance, $channel)
    {
        return method_exists(
            $instance, $channelMethod = Str::camel($channel).'Payload'
        ) ? $channelMethod : 'payload';
    }
}
