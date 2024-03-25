<?php

declare(strict_types=1);

namespace App\Common\Attribute\CliContract;


use App\Common\CliCommand\Interfaces\InputContractInterface;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class CliContract
{
    public function __construct(
        /** @var class-string<InputContractInterface> */
        public string $class
    ) {
    }
}
