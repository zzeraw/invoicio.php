<?php

namespace App\PhpStan\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @implements Rule<ClassMethod>
 */
final class DtoMethodsRule implements Rule
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

        $classReflection = $scope->getClassReflection();
        if (null === $classReflection) {
            return [];
        }

        if (!$this->isDtoClass($classReflection->getName(), $classReflection->getFileName())) {
            return [];
        }

        if ($node->isAbstract()) {
            return [];
        }

        $methodName = $node->name->toString();
        if ('__construct' === $methodName) {
            return [];
        }

        if ($this->isInterfaceMethod($classReflection, $methodName)) {
            return [];
        }

        if (!str_starts_with($methodName, 'get')) {
            return [
                sprintf(
                    'DTO method %s::%s() is not allowed. Only constructor and getters are permitted.',
                    $classReflection->getName(),
                    $methodName
                ),
            ];
        }

        if (!$this->isPlainGetter($node)) {
            return [
                sprintf(
                    'DTO getter %s::%s() must return a direct property value without transformation.',
                    $classReflection->getName(),
                    $methodName
                ),
            ];
        }

        return [];
    }

    private function isDtoClass(string $className, ?string $fileName): bool
    {
        if (false === str_ends_with($className, 'Dto')) {
            return false;
        }

        if (null === $fileName) {
            return false;
        }

        if (!str_contains($fileName, DIRECTORY_SEPARATOR . 'Dto' . DIRECTORY_SEPARATOR)) {
            return false;
        }

        return str_ends_with($fileName, 'Dto.php');
    }

    private function isInterfaceMethod(\PHPStan\Reflection\ClassReflection $classReflection, string $methodName): bool
    {
        foreach ($classReflection->getInterfaces() as $interface) {
            if ($interface->hasMethod($methodName)) {
                return true;
            }
        }

        return false;
    }

    private function isPlainGetter(ClassMethod $method): bool
    {
        $stmts = $method->getStmts();
        if (null === $stmts || count($stmts) !== 1) {
            return false;
        }

        $stmt = $stmts[0];
        if (!$stmt instanceof Return_) {
            return false;
        }

        $expr = $stmt->expr;
        if (!$expr instanceof PropertyFetch) {
            return false;
        }

        if (!$expr->var instanceof Variable || 'this' !== $expr->var->name) {
            return false;
        }

        return is_string($expr->name->toString());
    }
}
