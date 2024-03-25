<?php

declare(strict_types=1);

namespace App\Auth\Core\User\Application\UseCase\Create\Result;

use App\Auth\Core\User\Domain\User;

class Result
{
    public function __construct(
        public readonly ResultCase $case,
        public ?User $user = null,
    ) {
    }

    public static function success(User $user): self
    {
        return new Result(case: ResultCase::Success, user: $user);
    }

    public static function emailIsBusy(): self
    {
        return new Result(case: ResultCase::EmailIsBusy);
    }

    public function isSuccess(): bool
    {
        return $this->case->isEqual(ResultCase::Success);
    }

    public function isEmailIsBusy(): bool
    {
        return $this->case->isEqual(ResultCase::EmailIsBusy);
    }
}
