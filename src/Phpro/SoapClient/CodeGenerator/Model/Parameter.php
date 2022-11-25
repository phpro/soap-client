<?php

namespace Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Soap\Engine\Metadata\Model\Parameter as MetadataParameter;
use function Psl\Type\non_empty_string;

class Parameter
{
    /**
     * @var non-empty-string
     */
    private $name;

    /**
     * @var non-empty-string
     */
    private $type;

    /**
     * Parameter constructor.
     *
     * @param non-empty-string $name
     * @param non-empty-string $type
     */
    public function __construct(string $name, string $type)
    {
        $this->name = Normalizer::normalizeProperty($name);
        $this->type = Normalizer::normalizeClassnameInFQN($type);
    }

    public static function fromMetadata(string $parameterNamespace, MetadataParameter $parameter): Parameter
    {
        $type = non_empty_string()->assert($parameter->getType()->getBaseTypeOrFallbackToName());

        return new self(
            non_empty_string()->assert($parameter->getName()),
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
     * @return non-empty-string
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
