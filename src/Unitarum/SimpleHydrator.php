<?php

namespace Unitarum;

/**
 * Class SimpleHydrator
 * @package Unitarum
 */
class SimpleHydrator
{
    const METHOD_GET_PREFIX = 'get';
    const METHOD_SET_PREFIX = 'set';

    public function extract($entity): array
    {
        $array = [];

        $reflection = new \ReflectionClass($entity);
        foreach ($reflection->getMethods() as $method) {
            if (substr($method->getName(), 0, 3) == self::METHOD_GET_PREFIX && strlen($method->getName()) > 3) {
                $array[$this->convertNameTo($method->getName())] = $entity->{$method->getName()}();
            }
        }
        return $array;
    }

    public function hydrate(array $data, $entity)
    {
        foreach ($data as $name => $value) {
            $methodName = $this->convertNameFrom($name);
            $entity->$methodName($value);
        }

        return $entity;
    }

    protected function convertNameFrom($name): string
    {
        $parts = explode('_', $name);
        array_walk($parts, function (&$value) {
            $value = ucfirst($value);
        });
        return self::METHOD_SET_PREFIX . implode('', $parts);
    }

    protected function convertNameTo($name): string
    {
        $cleanName = lcfirst(substr($name, 3));
        $convertedName = strtolower(preg_replace('~[^a-z](A-Z)*~', '_$0', $cleanName));

        return $convertedName;
    }
}
