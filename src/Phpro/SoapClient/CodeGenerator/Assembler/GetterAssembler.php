<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Model\Property;
use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Phpro\SoapClient\CodeGenerator\LaminasCodeFactory\DocBlockGeneratorFactory;
use Phpro\SoapClient\Exception\AssemblerException;
use Laminas\Code\Generator\MethodGenerator;

/**
 * Class GetterAssembler
 *
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class GetterAssembler implements AssemblerInterface
{
    /**
     * @var GetterAssemblerOptions
     */
    private $options;

    /**
     * GetterAssembler constructor.
     *
     * @param GetterAssemblerOptions|null $options
     */
    public function __construct(GetterAssemblerOptions $options = null)
    {
        $this->options = $options ?? new GetterAssemblerOptions();
    }

    /**
     * {@inheritdoc}
     */
    public function canAssemble(ContextInterface $context): bool
    {
        return $context instanceof PropertyContext;
    }

    /**
     * @param ContextInterface|PropertyContext $context
     *
     * @throws AssemblerException
     */
    public function assemble(ContextInterface $context)
    {
        $class = $context->getClass();
        $property = $context->getProperty();
        try {
            $prefix = $this->getPrefix($property);
            $methodName = Normalizer::generatePropertyMethod($prefix, $property->getName());
            $class->removeMethod($methodName);

            $methodGenerator = new MethodGenerator($methodName);
            $methodGenerator->setVisibility(MethodGenerator::VISIBILITY_PUBLIC);
            $methodGenerator->setBody(sprintf('return $this->%s;', $property->getName()));

            if ($this->options->useReturnType()) {
                $methodGenerator->setReturnType($property->getCodeReturnType());
            }

            if ($this->options->useDocBlocks()) {
                $methodGenerator->setDocBlock(DocBlockGeneratorFactory::fromArray([
                    'tags' => [
                        [
                            'name'        => 'return',
                            'description' => $property->getType(),
                        ],
                    ],
                ]));
            }

            $class->addMethodFromGenerator($methodGenerator);
        } catch (\Exception $e) {
            throw AssemblerException::fromException($e);
        }
    }

    /**
     * @param Property $property
     * @return string
     */
    public function getPrefix(Property $property): string
    {
        if (!$this->options->useBoolGetters()) {
            return 'get';
        }

        return $property->getType() === 'bool' ? 'is' : 'get';
    }
}
