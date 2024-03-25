<?php

declare(strict_types=1);

namespace App\Common\CliCommand\Interfaces;

interface ValidatorServiceInterface
{
    public function validate(object $object): void;
}
