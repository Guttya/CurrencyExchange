<?php

declare(strict_types=1);

namespace App\Common\Model\Dispatcher;

use App\Common\Model\Dispatcher\Message\Message;
use App\Common\Model\EventDispatcher;
use Symfony\Component\Messenger\MessageBusInterface;

class MessengerEventDispatcher implements EventDispatcher
{
    public function __construct(
        private readonly MessageBusInterface $bus
    ) {
    }

    public function dispatch(array $events): void
    {
        foreach ($events as $event) {
            $this->bus->dispatch(new Message($event));
        }
    }
}
