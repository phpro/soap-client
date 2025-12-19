<?php

namespace Phpro\SoapClient\CodeGenerator\Config;

use Soap\Engine\Metadata\Model\XsdType;

final readonly class TypeNamespaceMap
{
    /**
     * @param Destination $fallback
     * @param array<non-empty-string, Destination> $map
     */
    public function __construct(
        private Destination $fallback,
        private array $map,
    ) {
    }

    public static function create(Destination $fallback): self
    {
        return new self($fallback, []);
    }

    public function withMapping(string $xmlns, Destination $destination): self
    {
        $newMap = $this->map;
        $newMap[$xmlns] = $destination;

        return new self($this->fallback, $newMap);
    }

    public function detectDestinationForType(XsdType $type): Destination
    {
        $xmlns = $type->getXmlNamespace(); // TODO : Is this the correct one?
        if ($xmlns && array_key_exists($xmlns, $this->map)) {
            return $this->map[$xmlns];
        }

        return $this->fallback;
    }
}
