<?php

declare(strict_types=1);

namespace Phpro\SoapClient\CodeGenerator\Rules;

use Phpro\SoapClient\CodeGenerator\CodingStandards\CodingStandardsStrategyInterface;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\Soap\Metadata\Detector\RequestTypesDetector;
use Soap\Engine\Metadata\Metadata;
use function Psl\Type\non_empty_string;

class IsRequestRule implements RuleInterface
{
    /**
     * @var array|null
     */
    private $requestTypes;

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
        if (!in_array($type->getName(), $this->listRequestTypes($codingStandards), true)) {
            return false;
        }

        return $this->subRule->appliesToContext($context);
    }

    public function apply(ContextInterface $context)
    {
        $this->subRule->apply($context);
    }

    private function listRequestTypes(CodingStandardsStrategyInterface $codingStandards): array
    {
        if (null === $this->requestTypes) {
            $this->requestTypes = array_map(
                fn (string $type) => $codingStandards->normalizeTypeName(non_empty_string()->assert($type)),
                (new RequestTypesDetector())($this->metadata->getMethods())
            );
        }

        return $this->requestTypes;
    }
}
