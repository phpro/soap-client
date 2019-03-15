<?php

namespace Phpro\SoapClient\Soap\ClassMap;

use IteratorAggregate;
use Phpro\SoapClient\Exception\InvalidArgumentException;

/**
 * Class ClassMapCollection
 *
 * @package Phpro\SoapClient\Soap\ClassMap
 */
class ClassMapCollection implements IteratorAggregate
{

    /**
     * @var array|ClassMapInterface[]
     */
    protected $classMaps = [];

    /**
     * @param array $classMaps
     */
    public function __construct(array $classMaps = [])
    {
        foreach ($classMaps as $classMap) {
            $this->add($classMap);
        }
    }

    /**
     * @param ClassMapInterface $classMap
     *
     * @return ClassMapCollection
     */
    public function add(ClassMapInterface $classMap): self
    {
        if ($this->has($classMap)) {
            throw new InvalidArgumentException('The classmap already exists!');
        }

        return $this->set($classMap);
    }

    /**
     * @param ClassMapInterface $classMap
     *
     * @return $this
     */
    public function set(ClassMapInterface $classMap): self
    {
        $this->classMaps[$classMap->getWsdlType()] = $classMap;

        return $this;
    }

    /**
     * @param ClassMapInterface $classMap
     *
     * @return bool
     */
    public function has(ClassMapInterface $classMap): bool
    {
        return array_key_exists($classMap->getWsdlType(), $this->classMaps);
    }

    /**
     * @return \ArrayIterator|ClassMapInterface[]
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->classMaps);
    }
}
