<?php

declare(strict_types=1);

namespace App\Common\Model;

interface EventDispatcher
{
    public function dispatch(array $events): void;
}
