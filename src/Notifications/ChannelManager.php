<?php

namespace Orchestra\Notifications;

use Illuminate\Notifications\ChannelManager as Manager;
use Illuminate\Notifications\Channels\MailChannel;

class ChannelManager extends Manager
{
    /**
     * Create an instance of the mail driver.
     *
     * @return \Illuminate\Notifications\Channels\MailChannel
     */
    protected function createMailDriver()
    {
        $mailer = $this->container->make('orchestra.postal');

        return $this->container->make(MailChannel::class, [$mailer->getMailer()]);
    }
}
