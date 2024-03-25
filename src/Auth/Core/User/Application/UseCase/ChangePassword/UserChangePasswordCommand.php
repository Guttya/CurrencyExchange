<?php

declare(strict_types=1);

namespace App\Auth\Core\User\Application\UseCase\ChangePassword;

use App\Auth\Core\User\Domain\ValueObject\Id;

class UserChangePasswordCommand
{
    public function __construct(
        public readonly Id $id,
        public readonly string $newPassword,
        public readonly string $oldPassword,
    ) {
    }
}
