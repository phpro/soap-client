<?php

declare(strict_types=1);

namespace Phpro\SoapClient\CodeGenerator\Rules;

use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\CodeGenerator\Context\PropertyContext;
use Phpro\SoapClient\CodeGenerator\Context\TypeContext;
use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Phpro\SoapClient\Soap\Metadata\Detector\RequestTypesDetector;
use Soap\Engine\Metadata\Metadata;
use function Psl\Type\non_empty_string;

class IsRequestRule implements RuleInterface
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
    private $requestTypes;

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
        if (!in_array($type->getName(), $this->listRequestTypes(), true)) {
            return false;
        }

        return $this->subRule->appliesToContext($context);
    }

    public function apply(ContextInterface $context)
    {
        $this->subRule->apply($context);
    }

    private function listRequestTypes(): array
    {
        if (null === $this->requestTypes) {
            $this->requestTypes = array_map(
                static function (string $type) {
                    return Normalizer::normalizeClassname(non_empty_string()->assert($type));
                },
                (new RequestTypesDetector())($this->metadata->getMethods())
            );
        }

        return $this->requestTypes;
    }
}
