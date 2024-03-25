<?php

declare(strict_types=1);

namespace App\Common\CliCommand\Console;

use App\Common\Attribute\CliContract\CliContract;
use App\Common\CliCommand\Interfaces\InputContractInterface;
use App\Common\CliCommand\Service\CliContractResolver;
use Exception;
use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Exception\ValidatorException;

abstract class CliCommand extends Command
{
    public function __construct(
        private readonly CliContractResolver $cliContractResolver,
    ) {
        parent::__construct();
    }

    /**
     * @description You can override this method and return your target class here, or use the CliContract attribute.
     *
     * @return class-string<InputContractInterface>
     * @throws Exception
     */
    protected function getInputContractClass(): string
    {
        $reflection = new ReflectionClass($this);
        $attributes = $reflection->getAttributes();
        foreach ($attributes as $attribute) {
            if (CliContract::class === $attribute->getName()) {
                $class = $attribute->getArguments()['class'] ?? NullInputContract::class;
                if (!class_exists($class)) {
                    throw new Exception(
                        sprintf(
                            '"%s" class not exists, check "%s" argument',
                            $class,
                            CliContract::class,
                        )
                    );
                }
                if (!is_subclass_of($class, InputContractInterface::class)) {
                    throw new Exception(
                        sprintf(
                            '"%s" is not subclass of "%s"',
                            $class,
                            InputContractInterface::class,
                        )
                    );
                }

                return $class;
            }
        }

        return NullInputContract::class;
    }

    protected function configure(): void
    {
        if ($this->autoconfigure()) {
            $inputContractClass = $this->getInputContractClass();
            $inputContract = new $inputContractClass();
            $reflectionClass = new ReflectionClass($inputContractClass);
            $properties = $reflectionClass->getProperties();
            $propertiesInfo = [];

            foreach ($properties as $property) {
                $propertyName = $property->getName();

                $propertyCommentRaw = $property->getDocComment();
                if (false === $propertyCommentRaw) {
                    $propertyCommentRaw = '';
                }
                $propertyComment = trim(str_replace(['/**', '*/', '/*'], '', $propertyCommentRaw));
                $isNullable = $property->getType()?->allowsNull();
                $propertyDefaultValue = $property->isInitialized($inputContract) ? $property->getValue($inputContract) : null;
                $propertiesInfo[$propertyName] = [
                    'name' => $propertyName,
                    'description' => $propertyComment,
                    'nullable' => $isNullable,
                    'default' => $propertyDefaultValue,
                ];
            }

            foreach ($propertiesInfo as $propertyInfo) {
                $this->addOption(
                    name: $propertyInfo['name'],
                    mode: $propertyInfo['nullable'] ? InputOption::VALUE_OPTIONAL : InputOption::VALUE_REQUIRED,
                    description: $propertyInfo['description'],
                    default: $propertyInfo['default'],
                );
            }
        }
    }

    abstract protected function handle(SymfonyStyle $io, InputContractInterface $inputContract): int;

    protected function autoconfigure(): bool
    {
        return true;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $inputContractClass = $this->getInputContractClass();
            /** @var InputContractInterface $inputContract */
            $inputContract = $this->cliContractResolver->resolve($input, $inputContractClass);
        } catch (ValidatorException $exception) {
            $violations = json_decode($exception->getMessage(), true, 512, JSON_THROW_ON_ERROR);
            $message = 'Command options has violations:' . PHP_EOL;

            $i = 0;
            foreach ($violations as $property => $violation) {
                ++$i;
                $message .= sprintf(
                        '%s. %s: %s',
                        $i,
                        $property,
                        $violation,
                    ) . PHP_EOL;
            }
            $io->error($message);

            return self::FAILURE;
        }

        return $this->handle(
            io: $io,
            inputContract: $inputContract,
        );
    }
}
