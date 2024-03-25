<?php

declare(strict_types=1);

namespace App\Auth\Core\User\Application\UseCase\ChangePassword\Result;

enum ResultCase
{
    case Success;
    case InvalidCredentials;

    public function isEqual(self $enum): bool
    {
        return $this->name === $enum->name;
    }
}
