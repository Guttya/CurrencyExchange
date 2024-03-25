<?php

declare(strict_types=1);

namespace App\Common\CliCommand\Service;

use App\Common\CliCommand\Interfaces\ValidatorServiceInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorService implements ValidatorServiceInterface
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly SerializerInterface $serializer,
    ) {
    }

    public function validate(object $object): void
    {
        /** @var ConstraintViolationList $violationList */
        $violationList = $this->validator->validate($object);
        $errors = [];
        foreach ($violationList as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }
        if ($violationList->count()) {
            $errorJson = $this->serializer->decode($errors);
            throw new ValidatorException($errorJson->getMessage()->all());
        }
    }
}