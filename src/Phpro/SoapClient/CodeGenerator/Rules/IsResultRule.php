<?php

declare(strict_types=1);

namespace Phpro\SoapClient\CodeGenerator\Rules;

use Phpro\SoapClient\CodeGenerator\CodingStandards\CodingStandardsStrategyInterface;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\Soap\Metadata\Detector\ResponseTypesDetector;
use Soap\Engine\Metadata\Metadata;
use function Psl\Type\non_empty_string;

class IsResultRule implements RuleInterface
{
    /**
     * @var array|null
     */
    private $responseTypes;

    public function __construct(
        private Metadata $metadata,
        private RuleInterface $subRule,
    ) {
    }

    public function appliesToContext(ContextInterface $context): bool
    {
        if (!$context instanceof TypeContext && !$context instanceof PropertyContext) {
            return false;
        }

        $type = $context->getType();
        $codingStandards = $context->getCodeGeneratorContext()->codingStandards;
        if (!in_array($type->getName(), $this->listResponseTypes($codingStandards), true)) {
            return false;
        }

        return $this->subRule->appliesToContext($context);
    }

    public function apply(ContextInterface $context)
    {
        $this->subRule->apply($context);
    }

    private function listResponseTypes(CodingStandardsStrategyInterface $codingStandards): array
    {
        if (null === $this->responseTypes) {
            $this->responseTypes = array_map(
                fn (string $type) => $codingStandards->normalizeTypeName(non_empty_string()->assert($type)),
                (new ResponseTypesDetector())($this->metadata->getMethods())
            );
        }

        return $this->responseTypes;
    }
}
