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
        if ($status === null) {
            throw new InvalidArgumentException('Status is invalid.');
        }

        return $status;
    }
}
