<?php

return [
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
    Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class => ['all' => true],
    App\AppBundle\AppBundle::class => ['all' => true],
    App\InvoiceBundle\InvoiceBundle::class => ['all' => true],
    App\UserBundle\UserBundle::class => ['all' => true],
    Nelmio\ApiDocBundle\NelmioApiDocBundle::class => ['all' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
];
