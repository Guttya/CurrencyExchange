<?php

declare(strict_types=1);

namespace App\Common\Model;

use Doctrine\ORM\EntityManagerInterface;

class Flusher
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EventDispatcher $dispatcher
    ) {
    }

    public function flush(AggregateRoot ...$roots): void
    {
        $this->em->flush();
        foreach ($roots as $root) {
            $this->dispatcher->dispatch($root->releaseEvents());
        }
    }
}
