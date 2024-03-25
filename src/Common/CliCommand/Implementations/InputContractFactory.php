<?php

declare(strict_types=1);

namespace App\Common\CliCommand\Implementations;

use App\Common\CliCommand\Exception\CliCommandException;
use App\Common\CliCommand\Exception\DeserializePayloadToInputContractException;
use App\Common\CliCommand\Interfaces\InputContractFactoryInterface;
use App\Common\CliCommand\Interfaces\InputContractInterface;
use App\Common\CliCommand\Interfaces\ValidatorServiceInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

readonly class InputContractFactory implements InputContractFactoryInterface
{
    public function __construct(
        private ValidatorServiceInterface $validator,
        private SerializerInterface $serializer
    ) {
    }

    /**
     * @param class-string<InputContractInterface> $contractClass
     * @param array<string, string>                $payload
     *
     * @throws CliCommandException
     * @throws \JsonException
     */
    public function resolve(string $contractClass, array $payload): InputContractInterface
    {
        if (!is_subclass_of($contractClass, InputContractInterface::class)) {
            throw new CliCommandException("{$contractClass} not is subclass of " . InputContractInterface::class, 400);
        }

        try {
            $inputContractDto = $this->serializer->decode(
                (array) json_encode($payload, JSON_THROW_ON_ERROR),
            );
        } catch (NotNormalizableValueException $exception) {
            throw new DeserializePayloadToInputContractException(
                message: 'Not normalizable value. Check that required fields are passed and they are not null, and fields type.',
                code: 400,
                previous: $exception,
                payload: $payload
            );
        }

        $this->validator->validate($inputContractDto);

        return $inputContractDto;
    }
}
