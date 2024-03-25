<?php

declare(strict_types=1);

namespace App\Auth\Core\User\Domain\Type;

use App\Auth\Core\User\Domain\ValueObject\Id;
use Assert\AssertionFailedException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\BigIntType;

class IdType extends BigIntType
{
    public const NAME = 'auth_user_id';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value instanceof Id ? $value->__toString() : $value;
    }

    /**
     * @throws AssertionFailedException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Id
    {
        return !empty($value) ? new Id((string) $value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
