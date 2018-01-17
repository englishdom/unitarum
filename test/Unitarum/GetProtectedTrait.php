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
}