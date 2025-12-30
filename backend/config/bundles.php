<?php

return [
    App\AdminBundle\AdminBundle::class => ['all' => true],
    App\AppBundle\AppBundle::class => ['all' => true],
    App\ApiBundle\ApiBundle::class => ['all' => true],
    App\InvoiceBundle\InvoiceBundle::class => ['all' => true],
    App\UserBundle\UserBundle::class => ['all' => true],
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
    Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class => ['all' => true],
    Nelmio\Alice\Bridge\Symfony\NelmioAliceBundle::class => ['test' => true],
    Nelmio\ApiDocBundle\NelmioApiDocBundle::class => ['all' => true],
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
];
