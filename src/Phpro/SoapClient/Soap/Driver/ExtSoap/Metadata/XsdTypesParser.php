<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata;

use Phpro\SoapClient\Soap\Driver\ExtSoap\AbusedClient;
use Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata\Visitor\ListVisitor;
use Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata\Visitor\SimpleTypeVisitor;
use Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata\Visitor\UnionVisitor;
use Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata\Visitor\XsdTypeVisitorInterface;
use Phpro\SoapClient\Soap\Engine\Metadata\Collection\XsdTypeCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\XsdType;

class XsdTypesParser
{
    /**
     * @var XsdTypeVisitorInterface[]
     */
    private $visitors;

    public function __construct(XsdTypeVisitorInterface ...$visitors)
    {
        $this->visitors = $visitors;
    }

    public static function default(): self
    {
        return new self(
            new ListVisitor(),
            new UnionVisitor(),
            new SimpleTypeVisitor()
        );
    }

    public function parse(AbusedClient $abusedClient): XsdTypeCollection
    {
        $collection = new XsdTypeCollection();
        $soapTypes = $abusedClient->__getTypes();
        foreach ($soapTypes as $soapType) {
            if ($type = $this->detectXsdType($soapType)) {
                $collection->add($type);
            }
        }

        return $collection;
    }

    private function detectXsdType(string $soapType): ?XsdType
    {
        foreach ($this->visitors as $visitor) {
            if ($type = $visitor($soapType)) {
                return $type;
            }
        }

        return null;
    }
}
