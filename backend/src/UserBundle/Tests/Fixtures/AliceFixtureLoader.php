<?php

namespace App\UserBundle\Tests\Fixtures;

use Doctrine\ORM\EntityManagerInterface;
use Nelmio\Alice\Loader\NativeLoader;

final class AliceFixtureLoader
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @return array<string, object>
     */
    public function load(string $path): array
    {
        $loader = new NativeLoader();
        $objectSet = $loader->loadFile($path);
        $objects = $objectSet->getObjects();

        foreach ($objects as $object) {
            $this->entityManager->persist($object);
        }

        $this->entityManager->flush();

        return $objects;
    }
}
