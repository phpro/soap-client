<?php

declare(strict_types=1);

namespace Phpro\SoapClient\CodeGenerator\Rules;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Phpro\SoapClient\Soap\Metadata\Detector\ResponseTypesDetector;
use Soap\Engine\Metadata\Metadata;
use function Psl\Type\non_empty_string;

class IsResultRule implements RuleInterface
{
    /**
     * @var Metadata
     */
    private $metadata;

    /**
     * @var RuleInterface
     */
    private $subRule;

    /**
     * @var array|null
     */
    private $responseTypes;

    public function __construct(Metadata $metadata, RuleInterface $subRule)
    {
        $this->metadata = $metadata;
        $this->subRule = $subRule;
    }

    public function appliesToContext(ContextInterface $context): bool
    {
        if (!$context instanceof TypeContext && !$context instanceof PropertyContext) {
            return false;
        }

        $type = $context->getType();
        if (!in_array($type->getName(), $this->listResponseTypes(), true)) {
            return false;
        }

        return $this->subRule->appliesToContext($context);
    }

    public function apply(ContextInterface $context)
    {
        $this->subRule->apply($context);
    }

    private function listResponseTypes(): array
    {
        if (null === $this->responseTypes) {
            $this->responseTypes = array_map(
                static function (string $type) {
                    return Normalizer::normalizeClassname(non_empty_string()->assert($type));
                },
                (new ResponseTypesDetector())($this->metadata->getMethods())
            );
        }

        return $this->responseTypes;
    }
}
