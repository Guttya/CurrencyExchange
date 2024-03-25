<?php

declare(strict_types=1);

namespace App\Auth\Entry\Cli\CreateAdmin;

use App\Auth\Core\User\Application\UseCase\Create\UserCreateCommand;
use App\Auth\Core\User\Domain\UserRepository;
use App\Common\Attribute\CliContract\CliContract;
use App\Common\CliCommand\Console\CliCommand;
use App\Common\CliCommand\Interfaces\InputContractInterface;
use App\Common\CliCommand\Service\CliContractResolver;
use Assert\Assertion;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Auth\Core\User\Application\UseCase\Create\UserCreateHandler;

#[AsCommand(
    name: 'admin:create',
    description: 'Create root user for admin panel',
)]
#[CliContract(class: AdminCreateRootCliContract::class)]
class AdminCreateRootCommand extends CliCommand
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserCreateHandler $handler,
        CliContractResolver $cliContractResolver,
    ) {
        parent::__construct($cliContractResolver);
    }

    protected function handle(SymfonyStyle $io, InputContractInterface $inputContract): int
    {
        Assertion::isInstanceOf($inputContract, AdminCreateRootCliContract::class);

        $password = $inputContract->password;
        if (null === $password) {
            $password = bin2hex(random_bytes(8));
            $io->note(sprintf('Using generated password: %s', $password));
        }

        $user = $this->userRepository->findOneBy(['email' => $inputContract->email]);
        if (null !== $user) {
            $io->error('Root user is already exists');

            return Command::FAILURE;
        }

        $this->handler->handle(
            new UserCreateCommand(
                plainPassword: $password,
                name: 'Admin',
                email: $inputContract->email
            )
        );

        $io->success('Admin user is created');

        return Command::SUCCESS;
    }
}
