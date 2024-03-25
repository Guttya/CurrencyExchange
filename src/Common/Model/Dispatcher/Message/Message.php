<?php

declare(strict_types=1);

namespace App\Common\Model\Dispatcher\Message;

class Message
{
    public function __construct(private readonly object $event)
    {
    }

    public function getEvent(): object
    {
        return $this->event;
    }
}
