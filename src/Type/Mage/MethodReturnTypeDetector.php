<?php
declare(strict_types=1);

namespace PHPStanMagento1\Type\Mage;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;

abstract class MethodReturnTypeDetector
{
    abstract protected static function getMethodName(): string;
    abstract protected function getMagentoClassName(string $identifier): string;

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === $this::getMethodName();
    }

    /**
     * @throws \PHPStan\ShouldNotHappenException
     */
    public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): Type
    {
        return $this->getTypeFromExpr($methodReflection, $methodCall, $scope);
    }

    /**
     * @param MethodCall|\PhpParser\Node\Expr\StaticCall $methodCall
     * @throws \PHPStan\ShouldNotHappenException
     */
    protected function getTypeFromExpr(MethodReflection $methodReflection, $methodCall, Scope $scope): Type
    {
        if (! isset($methodCall->args[0]) || ! $methodCall->args[0]->value instanceof String_) {
            throw new ShouldNotHappenException();
        }

        $modelName = $methodCall->args[0]->value->value;
        $modelClassName = $this->getMagentoClassName($modelName);

        return new ObjectType($modelClassName);
    }

    protected function getMagentoConfig(): \Mage_Core_Model_Config
    {
        return \Mage::app()->getConfig();
    }
}