<?php

namespace Phpro\SoapClient\CodeGenerator\Model;

use Soap\Engine\Metadata\Collection\MethodCollection;
use Soap\Engine\Metadata\Model\Method;

/**
 * Class ClientMethodMap
 *
 * @package Phpro\SoapClient\CodeGenerator\Model
 */
class ClientMethodMap
{
    /**
     * @var ClientMethod[]
     */
    private $methods;

    /**
     * ClientMethodMap constructor.
     *
     * @param array|ClientMethod[] $methods
     */
    public function __construct(array $methods)
    {
        $this->methods = $methods;
    }

    /**
     * @param non-empty-string $parameterNamespace
     */
    public static function fromMetadata(string $parameterNamespace, MethodCollection $collection): self
    {
        return new self($collection->map(function (Method $method) use ($parameterNamespace) {
            return ClientMethod::fromMetadata($parameterNamespace, $method);
        }));
    }

    /**
     * @return ClientMethod[]
     */
    public function getMethods() : array
    {
        return $this->methods;
    }
}
