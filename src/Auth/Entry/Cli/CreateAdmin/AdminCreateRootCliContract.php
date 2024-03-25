<?php

declare(strict_types=1);

namespace App\Auth\Entry\Cli\CreateAdmin;

use Symfony\Component\Validator\Constraints as Assert;
use App\Common\CliCommand\Interfaces\InputContractInterface;

class AdminCreateRootCliContract implements InputContractInterface
{
    /** Root user email. */
    #[Assert\Email]
    #[Assert\Type('string')]
    public ?string $email = 'admin@gmail.com';

    /** Root user password. */
    #[Assert\Type('string')]
    public ?string $password = 'root';
}
