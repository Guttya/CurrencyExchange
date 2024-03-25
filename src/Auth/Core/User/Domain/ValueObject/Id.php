<?php

declare(strict_types=1);

namespace App\Auth\Core\User\Domain\ValueObject;

use Assert\Assertion;
use Assert\AssertionFailedException;

class Id
{
    /**
     * @throws AssertionFailedException
     */
    public function __construct(
        private readonly string $value
    ) {
        Assertion::notEmpty($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
