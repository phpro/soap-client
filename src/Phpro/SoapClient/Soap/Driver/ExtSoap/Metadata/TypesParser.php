<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata;

use Phpro\SoapClient\Soap\Driver\ExtSoap\AbusedClient;
use Phpro\SoapClient\Soap\Engine\Metadata\Collection\TypeCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\Collection\XsdTypeCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Property;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Type;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\XsdType;

class TypesParser
{
    /**
     * @var XsdTypeCollection
     */
    private $xsdTypes;

    public function __construct(XsdTypeCollection $xsdTypes)
    {
        $this->xsdTypes = $xsdTypes;
    }

    public function parse(AbusedClient $abusedClient): TypeCollection
    {
        $collection = new TypeCollection();
        $soapTypes = $abusedClient->__getTypes();
        foreach ($soapTypes as $soapType) {
            $properties = [];
            $lines = explode("\n", $soapType);
            if (!preg_match('/struct (?P<typeName>.*) {/', $lines[0], $matches)) {
                continue;
            }
            $xsdType = XsdType::create($matches['typeName']);

            foreach (array_slice($lines, 1) as $line) {
                if ($line === '}') {
                    continue;
                }
                preg_match('/\s* (?P<propertyType>.*) (?P<propertyName>.*);/', $line, $matches);
                $properties[] = new Property(
                    $matches['propertyName'],
                    $this->xsdTypes->fetchByNameWithFallback($matches['propertyType'])
                );
            }

            $collection->add(new Type($xsdType, $properties));
        }

        return $collection;
    }
}
