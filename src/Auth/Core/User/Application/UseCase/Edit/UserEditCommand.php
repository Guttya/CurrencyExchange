<?php

declare(strict_types=1);

namespace App\Auth\Core\User\Application\UseCase\Edit;

use App\Auth\Core\User\Domain\ValueObject\Id;

class UserEditCommand
{
    public function __construct(
        public readonly Id $id,
        public readonly ?string $name,
        public readonly ?string $email,
    ) {
    }
}
