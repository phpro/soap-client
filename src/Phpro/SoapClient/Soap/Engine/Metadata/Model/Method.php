<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Engine\Metadata\Model;

class Method
{
    /**
     * @var Parameter[]
     */
    private $parameters = [];

    /**
     * @var string
     */
    private $name;

    /**
     * @var XsdType
     */
    private $returnType;

    public function __construct(string $name, array $parameters, XsdType $returnType)
    {
        $this->name = $name;
        $this->returnType = $returnType;
        foreach ($parameters as $parameter) {
            $this->addParameter($parameter);
        }
    }

    /**
     * @return array|Parameter[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getReturnType(): XsdType
    {
        return $this->returnType;
    }

    public function addParameter(Parameter $parameter): void
    {
        $this->parameters[] = $parameter;
    }
}
