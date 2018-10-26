<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata;

use Phpro\SoapClient\Soap\Driver\ExtSoap\AbusedClient;
use Phpro\SoapClient\Soap\Engine\Metadata\Collection\TypeCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Property;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Type;

class TypesParser
{
    public function parse(AbusedClient $abusedClient): TypeCollection
    {
        $collection = new TypeCollection();
        $soapTypes = $abusedClient->__getTypes();
        foreach ($soapTypes as $soapType) {
            $properties = [];
            $lines = explode("\n", $soapType);
            if (!preg_match('/struct (.*) {/', $lines[0], $matches)) {
                continue;
            }
            $typeName = $matches[1];

            foreach (array_slice($lines, 1) as $line) {
                if ($line === '}') {
                    continue;
                }
                preg_match('/\s* (.*) (.*);/', $line, $matches);
                $properties[] = new Property($matches[2], $matches[1]);
            }

            $collection->add(new Type($typeName, $properties));
        }

        return $collection;
    }
}
