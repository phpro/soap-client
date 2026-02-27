<?php

namespace Phpro\SoapClient\CodeGenerator\Config;

use Soap\Engine\Metadata\Model\XsdType;

/**
 * @psalm-type Strategy = \Closure(string $xmlns, Destination $fallback): Destination
 */
final readonly class TypeNamespaceMap
{
    /**
     * @param Destination $fallback
     * @param array<non-empty-string, Destination> $map
     * @param Strategy|null $strategy
     */
    public function __construct(
        private Destination $fallback,
        private array $map = [],
        private ?\Closure $strategy = null,
    ) {
    }

    public static function create(Destination $fallback): self
    {
        return new self($fallback);
    }

    public function withMapping(string $xmlns, Destination $destination): self
    {
        $newMap = $this->map;
        $newMap[$xmlns] = $destination;

        return new self($this->fallback, $newMap);
    }

    /**
     * Add a strategy to determine the destination for a given xmlns.
     * The strategy is a callable that takes the xmlns and the fallback destination as arguments
     * and returns a "calculated" destination.
     *
     * If the xmlns exists in the map, the strategy will not be called.
     * If the xmlns does not exist in the map,
     * the strategy will be called with the xmlns and the fallback destination as arguments.
     *
     * @param Strategy|null $strategy
     */
    public function withStrategy(?\Closure $strategy): self
    {
        return new self($this->fallback, $this->map, $strategy);
    }

    public function detectDestinationForType(XsdType $type): Destination
    {
        $xmlns = $type->getXmlNamespace(); // TODO : Is this the correct one?
        if ($xmlns && array_key_exists($xmlns, $this->map)) {
            return $this->map[$xmlns];
        }

        $fallback = $this->fallback;
        if ($this->strategy !== null) {
            $fallback = ($this->strategy)($xmlns, $fallback);
        }

        return $fallback;
    }
}
