<?php

namespace App\Message;

interface NotificationInterface
{
    public function process(MessageInterface $message): void;
}
