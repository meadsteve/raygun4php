<?php

namespace MeadSteve\Raygun4php\Senders;

use MeadSteve\Raygun4php\Message;

interface MessageSender
{
    public function Send(Message $message);
}
