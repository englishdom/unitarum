<?php

namespace UnitarumTest;

trait GetProtectedTrait
{
    protected static function getProtectedMethod($className, $methodName) {
        $class = new \ReflectionClass($className);
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);
        return $method;
    }

    protected static function getProtectedProperty($className, $propertyName)
    {
        $class = new \ReflectionClass($className);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        return $property;
    }
}