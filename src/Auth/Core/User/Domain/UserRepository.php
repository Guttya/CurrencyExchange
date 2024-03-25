<?php

declare(strict_types=1);

namespace App\Auth\Core\User\Domain;

use App\Auth\Core\User\Domain\ValueObject\Id;
use Assert\AssertionFailedException;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Config\Resource\ResourceInterface;

class UserRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, $em->getClassMetadata(User::class));
    }

    public function add(ResourceInterface $resource): void
    {
        $this->getEntityManager()->persist($resource);
    }

    public function remove(ResourceInterface $resource): void
    {
        $this->getEntityManager()->remove($resource);
    }

    public function findById(Id $id): ?User
    {
        /** @var User|null $user */
        $user = $this->findOneBy(['id' => $id]);

        return $user;
    }

    /**
     * @throws AssertionFailedException
     * @throws Exception
     */
    public function nextId(): Id
    {
        $id = $this->getEntityManager()
            ->getConnection()
            ->prepare('SELECT nextval(\'auth_user_id_seq\')')
            ->executeQuery()
            ->fetchOne();

        return new Id((string) $id);
    }

    public function hasByEmail(string $email): bool
    {
        return null !== $this->findOneBy(['email' => $email]);
    }
}
