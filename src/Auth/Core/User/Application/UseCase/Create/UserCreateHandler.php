<?php

declare(strict_types=1);

namespace App\Auth\Core\User\Application\UseCase\Create;

use App\Auth\Core\User\Application\UseCase\Create\Result\Result;
use App\Auth\Core\User\Domain\User;
use App\Auth\Core\User\Domain\UserRepository;
use App\Common\Model\Flusher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCreateHandler
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $em,
        private readonly Flusher $flusher,
    ) {
    }

    public function handle(UserCreateCommand $command): Result
    {
        if ($this->userRepository->hasByEmail($command->email)) {
            return Result::emailIsBusy();
        }

        $now = new \DateTimeImmutable();
        $user = User::create(
            id: $this->userRepository->nextId(),
            createdAt: $now,
            updatedAt: $now,
            email: $command->email,
            roles: [User::ROLE_ADMIN],
            name: $command->name
        );

        $this->em->persist($user);
        $user->changePassword(
            $this->passwordHasher->hashPassword($user, $command->plainPassword)
        );

        $this->flusher->flush($user);

        return Result::success($user);
    }
}
