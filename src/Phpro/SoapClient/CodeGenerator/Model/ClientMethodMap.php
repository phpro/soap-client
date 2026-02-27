<?php

namespace Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Context\CodeGeneratorContext;
use Soap\Engine\Metadata\Collection\MethodCollection;
use Soap\Engine\Metadata\Model\Method;

final readonly class ClientMethodMap
{
    /**
     * @internal - Use ClientMethodMap::fromMetadata instead
     *
     * @param array<array-key, ClientMethod> $methods
     */
    public function __construct(
        private array $methods
    ) {
    }

    public static function fromMetadata(
        CodeGeneratorContext $codeGeneratorContext,
        MethodCollection $collection,
    ): self {
        return new self($collection->map(function (Method $method) use ($codeGeneratorContext) {
            return ClientMethod::fromMetadata($codeGeneratorContext, $method);
        }));
    }

    /**
     * @return array<array-key, ClientMethod>
     */
    public function getMethods(): array
    {
        return $this->methods;
    }
}
