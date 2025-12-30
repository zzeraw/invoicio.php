<?php

namespace App\UserBundle\Enum;

use InvalidArgumentException;

enum UserStatusEnum: string
{
    case ACTIVE = 'active';
    case BLOCKED = 'blocked';

    public static function fromString(string $value): self
    {
        $status = self::tryFrom($value);
        if (null === $status) {
            throw new InvalidArgumentException('Status is invalid.');
        }

        return $status;
    }
}
