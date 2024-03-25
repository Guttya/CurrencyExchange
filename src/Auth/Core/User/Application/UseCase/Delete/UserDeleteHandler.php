<?php

declare(strict_types=1);

namespace App\Auth\Core\User\Application\UseCase\Delete;

use App\Auth\Core\User\Application\UseCase\Delete\Result\Result;
use App\Auth\Core\User\Domain\UserRepository;
use App\Common\Model\Flusher;

class UserDeleteHandler
{
    public function __construct(
        private readonly Flusher $flusher,
        private readonly UserRepository $userRepository,
    ) {
    }

    public function handle(UserDeleteCommand $command): Result
    {
        $user = $this->userRepository->findById($command->id);
        if (null === $user) {
            return Result::userNotExists();
        }

        $this->userRepository->remove($user);
        $this->flusher->flush($user);

        return Result::success($user);
    }
}
