<?php

namespace Phpro\SoapClient\CodeGenerator\Model;

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
        $this->name = $name;
        $this->type = $type;
    }

    public static function fromMetadata(string $parameterNamespace, MetadataParameter $parameter)
    {
        return new self(
            $parameter->getName(),
            $parameterNamespace.'\\'.$parameter->getType()
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
