<?php

namespace App\UserBundle\Enum;

use InvalidArgumentException;

enum UserRoleEnum: string
{
    case ADMIN = 'admin';
    case USER = 'user';

    public static function fromString(string $value): self
    {
        $role = self::tryFrom($value);
        if (null === $role) {
            throw new InvalidArgumentException('Role is invalid.');
        }

        return $role;
    }
}
