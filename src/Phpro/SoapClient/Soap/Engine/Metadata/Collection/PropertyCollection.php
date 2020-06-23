<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Engine\Metadata\Collection;

use Phpro\SoapClient\Soap\Engine\Metadata\Model\Property;

class PropertyCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var Property[]
     */
    private $properties;

    public function __construct(Property ...$properties)
    {
        $this->properties = $properties;
    }

    /**
     * @return \ArrayIterator|Property[]
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->properties);
    }

    public function count(): int
    {
        return count($this->properties);
    }

    public function map(callable  $callback): array
    {
        return array_map($callback, $this->properties);
    }

    public function mapNames(): array
    {
        return $this->map(static function (Property $property): string {
            return $property->getName();
        });
    }

    public function unique(): self
    {
        return new PropertyCollection(...array_values(
            array_combine(
                $this->mapNames(),
                $this->properties
            )
        ));
    }
}
