<?php

declare(strict_types=1);

namespace App\Common\Model;

interface AggregateRoot
{
    public function releaseEvents(): array;
}
