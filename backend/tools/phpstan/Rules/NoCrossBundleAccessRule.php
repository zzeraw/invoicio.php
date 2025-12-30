<?php

namespace App\PhpStan\Rules;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\NodeFinder;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @implements Rule<Class_>
 */
final class NoCrossBundleAccessRule implements Rule
{
    private const ALLOWED_SEGMENTS = ['\\Enum\\', '\\PublicService\\', '\\PublicInterface\\'];

    private NodeFinder $nodeFinder;

    public function __construct()
    {
        $this->nodeFinder = new NodeFinder();
    }

    public function getNodeType(): string
    {
        return Class_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node instanceof Class_) {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        if (null === $classReflection) {
            return [];
        }

        $className = $classReflection->getName();
        $currentBundle = $this->getBundleName($className);
        if (null === $currentBundle) {
            return [];
        }

        $names = $this->nodeFinder->findInstanceOf($node, Name::class);
        $errors = [];

        foreach ($names as $name) {
            if (!$name instanceof Name) {
                continue;
            }

            if ('self' === $name->toString() || 'static' === $name->toString() || 'parent' === $name->toString()) {
                continue;
            }

            $resolvedName = $scope->resolveName($name);
            if (!is_string($resolvedName) || !str_starts_with($resolvedName, 'App\\')) {
                continue;
            }

            $targetBundle = $this->getBundleName($resolvedName);
            if (null === $targetBundle) {
                continue;
            }

            if ($targetBundle === $currentBundle) {
                continue;
            }

            if ($this->isAllowedCrossBundleReference($resolvedName)) {
                continue;
            }

            $errors[] = sprintf(
                'Class %s must not reference %s from another bundle.',
                $className,
                $resolvedName
            );
        }

        return array_values(array_unique($errors));
    }

    private function getBundleName(string $className): ?string
    {
        if (preg_match('/^App\\\\([^\\\\]+Bundle)\\\\/', $className, $matches) !== 1) {
            return null;
        }

        return $matches[1];
    }

    private function isAllowedCrossBundleReference(string $className): bool
    {
        foreach (self::ALLOWED_SEGMENTS as $segment) {
            if (str_contains($className, $segment)) {
                return true;
            }
        }

        return false;
    }
}
