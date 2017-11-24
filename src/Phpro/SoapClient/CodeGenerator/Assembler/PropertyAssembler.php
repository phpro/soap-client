<?php

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\Exception\AssemblerException;
use Zend\Code\Generator\PropertyGenerator;
use Zend\Code\Generator\DocBlockGenerator;

/**
 * Class PropertyAssembler
 *
 * @package Phpro\SoapClient\CodeGenerator\Assembler
 */
class PropertyAssembler implements AssemblerInterface
{
    /**
     * @var string
     */
    private $visibility;

    /**
     * PropertyAssembler constructor.
     * @param string $visibility
     */
    public function __construct(string $visibility = PropertyGenerator::VISIBILITY_PRIVATE)
    {
        $this->visibility = $visibility;
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
            // It's not possible to overwrite a property in zend-code yet!
            if ($class->hasProperty($property->getName())) {
                return;
            }

            $class->addPropertyFromGenerator(
                PropertyGenerator::fromArray([
                    'name' => $property->getName(),
                    'visibility' => $this->visibility,
                    'omitdefaultvalue' => true,
                    'docblock' => DocBlockGenerator::fromArray([
                        'tags' => [
                            [
                                'name'        => 'var',
                                'description' => $property->getType(),
                            ],
                        ]
                    ])
                ])
            );
        } catch (\Exception $e) {
            throw AssemblerException::fromException($e);
        }
    }
}
