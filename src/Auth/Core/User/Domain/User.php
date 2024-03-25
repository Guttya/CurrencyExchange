<?php

declare(strict_types=1);

namespace App\Auth\Core\User\Domain;

use App\Auth\Core\User\Domain\Type\IdType;
use App\Auth\Core\User\Domain\ValueObject\Id;
use App\Common\Model\EventsTrait;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'auth_users')]
#[UniqueEntity(fields: ['email'])]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use EventsTrait;

    public const ROLE_ADMIN = 'ROLE_ADMIN';

    #[ORM\Id]
    #[ORM\Column(type: IdType::NAME, nullable: false)]
    private Id $id;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $email;

    #[ORM\Column(type: 'json', options: ['default' => '[]'])]
    private array $userRoles = [];

    #[ORM\Column(type: 'string')]
    private string $password;

    private ?string $plainPassword = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    public function __construct(
        Id $id,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function create(
        Id $id,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
        string $email,
        array $roles,
        string $name,
    ): self {
        $user = new self(
            id: $id,
            createdAt: $createdAt,
            updatedAt: $updatedAt,
        );
        $user->email = $email;
        $user->userRoles = $roles;
        $user->name = $name;

        return $user;
    }

    public static function setManagerType(
        Id $id,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
        string $email,
        array $roles,
        string $name,
    ): self {
        $user = new self(
            id: $id,
            createdAt: $createdAt,
            updatedAt: $updatedAt,
        );
        $user->email = $email;
        $user->userRoles = $roles;
        $user->name = $name;

        return $user;
    }


    public function edit(string $name, string $email): void
    {
        $this->email = $email;
        $this->name = $name;
    }

    public function changePassword(string $password): void
    {
        $this->password = $password;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    public function getRoles(): array
    {
        $roles = $this->userRoles;
        // guarantee every user at least has ROLE_ADMIN
        $roles[] = self::ROLE_ADMIN;

        return array_unique($roles);
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PreUpdate]
    public function onUpdated(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    /**
     * @internal
     */
    public function setUserRoles(array $userRoles): void
    {
        $this->userRoles = $userRoles;
    }

    /**
     * @internal
     */
    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @internal
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = '';
    }

    /**
     * @internal
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }
}
