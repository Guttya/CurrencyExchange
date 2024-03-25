<?php

declare(strict_types=1);

namespace App\Auth\Core\User\Application\UseCase\Delete;

use App\Auth\Core\User\Domain\ValueObject\Id;

class UserDeleteCommand
{
    public function __construct(
        public readonly Id $id,
    ) {
    }
}
