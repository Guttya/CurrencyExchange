<?php

declare(strict_types=1);

namespace App\Auth\Core\User\Application\UseCase\Create;

class UserCreateCommand
{
    public function __construct(
        public readonly string $plainPassword,
        public readonly string $name,
        public readonly string $email,
    ) {
    }
}
