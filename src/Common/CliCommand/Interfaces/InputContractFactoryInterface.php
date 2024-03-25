<?php

declare(strict_types=1);

namespace App\Common\CliCommand\Interfaces;

interface InputContractFactoryInterface
{
    public function resolve(string $contractClass, array $payload): InputContractInterface;
}
