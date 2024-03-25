<?php

declare(strict_types=1);

namespace App\Common\CliCommand\Service;

use App\Common\CliCommand\Interfaces\InputContractFactoryInterface;
use App\Common\CliCommand\Interfaces\InputContractInterface;
use Symfony\Component\Console\Input\InputInterface;

readonly class CliContractResolver
{
    public function __construct(
        private InputContractFactoryInterface $inputContractResolver,
    ) {
    }

    /**
     * @param class-string<InputContractInterface> $contractClass
     */
    public function resolve(InputInterface $input, string $contractClass): InputContractInterface
    {
        /** @var array<string, string> $payload */
        $payload = array_merge(
            $input->getOptions(),
            $input->getArguments(),
        );

        return $this->inputContractResolver->resolve($contractClass, $payload);
    }
}
