<?php

declare(strict_types=1);

namespace App\Common\CliCommand\Exception;

class CliCommandException extends \DomainException
{
    public function __construct(
        string $message = '',
        ?int $code = 500,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, (int) $code, $previous);
    }
}
