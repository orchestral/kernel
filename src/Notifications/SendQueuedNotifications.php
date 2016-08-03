<?php

namespace Orchestra\Notifications;

use Illuminate\Notifications\SendQueuedNotifications as BaseSendQueuedNotifications;

class SendQueuedNotifications extends BaseSendQueuedNotifications
{
    /**
     * Send the notifications.
     *
     * @param  \Orchestra\Notifications\ChannelManager  $manager
     * @return void
     */
    public function handle(ChannelManager $manager)
    {
        $manager->sendNow($this->notifiables, $this->notification);
    }
}
