<?php

namespace Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Parameter as MetadataParameter;

class Parameter
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * Parameter constructor.
     *
     * @param string $name
     * @param string $type
     */
    public function __construct(string $name, string $type)
    {
        $this->name = Normalizer::normalizeProperty($name);
        $this->type = Normalizer::normalizeClassnameInFQN($type);
    }

    public static function fromMetadata(string $parameterNamespace, MetadataParameter $parameter): Parameter
    {
        $type = $parameter->getType()->getBaseTypeOrFallbackToName();

        return new self(
            $parameter->getName(),
            Normalizer::isKnownType($type)
                ? $type
                : $parameterNamespace.'\\'.$type
        );
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get an array representation for creating a Generator
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
        ];
    }
}
