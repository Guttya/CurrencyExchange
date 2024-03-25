<?php

declare(strict_types=1);

namespace App\Common\Model\Dispatcher;

interface NamedEvent
{
    public static function getEventName(): string;
}
