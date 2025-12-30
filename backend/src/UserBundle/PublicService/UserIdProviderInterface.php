<?php

namespace App\UserBundle\PublicService;

interface UserIdProviderInterface
{
    public function getIdByEmail(string $email): int;
}
