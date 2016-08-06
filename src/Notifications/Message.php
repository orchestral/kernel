<?php

namespace Orchestra\Notifications;

use Illuminate\Notifications\Message as BaseMessage;

class Message extends BaseMessage
{
    use MessageTitle;
}
