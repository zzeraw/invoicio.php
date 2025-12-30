<?php

namespace App\PhpStan\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Type\Type;
use PHPStan\Type\TypeUtils;

/**
 * @implements Rule<ClassMethod>
 */
final class NoPublicMethodReturnsEntityRule implements Rule
{
    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node instanceof ClassMethod) {
            return [];
        }

        if (!$node->isPublic() || $this->isConstructor($node)) {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return [];
        }

        $className = $classReflection->getName();
        if (!$this->isBundleClass($className) || $this->isEntityClass($className)) {
            return [];
        }

        $methodName = $node->name->toString();
        if (!$classReflection->hasMethod($methodName)) {
            return [];
        }

        $methodReflection = $classReflection->getMethod($methodName, $scope);
        $messages = [];

        foreach ($methodReflection->getVariants() as $variant) {
            $returnType = $variant->getReturnType();
            $entityClasses = $this->getEntityClassesFromType($returnType);
            if ($entityClasses === []) {
                continue;
            }

            $messages[] = sprintf(
                'Public method %s::%s() returns entity type(s): %s.',
                $className,
                $methodName,
                implode(', ', $entityClasses)
            );
        }

        return $messages;
    }

    /**
     * @return array<int, string>
     */
    private function getEntityClassesFromType(Type $type): array
    {
        $classes = [];
        foreach ($type->getReferencedClasses() as $className) {
            if ($this->isEntityClass($className)) {
                $classes[] = $className;
            }
        }

        return array_values(array_unique($classes));
    }

    private function isEntityClass(string $className): bool
    {
        return str_contains($className, '\\Entity\\');
    }

    private function isBundleClass(string $className): bool
    {
        return str_starts_with($className, 'App\\') && str_contains($className, 'Bundle\\');
    }

    private function isConstructor(ClassMethod $method): bool
    {
        return $method->name->toString() === '__construct';
    }
}
