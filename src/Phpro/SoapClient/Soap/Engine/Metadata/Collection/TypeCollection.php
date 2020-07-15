<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Engine\Metadata\Collection;

use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Phpro\SoapClient\Exception\MetadataException;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Type;

class TypeCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var Type[]
     */
    private $types;

    public function __construct(Type ...$types)
    {
        $this->types = $types;
    }

    /**
     * @return \ArrayIterator|Type[]
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->types);
    }

    public function count(): int
    {
        return count($this->types);
    }

    public function add(Type $type)
    {
        $this->types[] = $type;
    }

    public function addMany(TypeCollection $types)
    {
        foreach ($types as $type) {
            $this->add($type);
        }
    }

    public function map(callable  $callback): array
    {
        return array_map($callback, $this->types);
    }

    public function filter(callable $filter): self
    {
        return new self(...array_filter(
            $this->types,
            $filter
        ));
    }

    public function reduce(callable $reducer, $initial = null)
    {
        return array_reduce(
            $this->types,
            $reducer,
            $initial
        );
    }

    public function fetchOneByName(string $name): Type
    {
        foreach ($this->types as $type) {
            if ($name === $type->getName()) {
                return $type;
            }
        }

        throw MetadataException::typeNotFound($name);
    }

    public function fetchAllByNormalizedName(string $name): TypeCollection
    {
        return $this->filter(static function (Type $type) use ($name): bool {
            return Normalizer::normalizeClassname($type->getName()) === $name;
        });
    }
}
