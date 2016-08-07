<?php

namespace Orchestra\Notifications;

use Illuminate\Notifications\Channels\MailChannel;
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
        $mailer = $this->app->make('orchestra.mail');

        return $this->app->make(MailChannel::class, [$mailer->getMailer()]);
    }
}
