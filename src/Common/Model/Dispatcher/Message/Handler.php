<?php

declare(strict_types=1);

namespace App\Common\Model\Dispatcher\Message;

use App\Common\Model\Dispatcher\NamedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class Handler
{
    public function __construct(
        private readonly EventDispatcherInterface $dispatcher
    ) {
    }

    public function __invoke(Message $message): void
    {
        $event = $message->getEvent();
        $eventName = $event instanceof NamedEvent ? $event::getEventName() : get_class($event);
        $this->dispatcher->dispatch(
            $event,
            $eventName
        );
    }
}
