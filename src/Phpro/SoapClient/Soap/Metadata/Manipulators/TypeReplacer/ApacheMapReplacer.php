<?php
declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Metadata\Manipulators\TypeReplacer;

use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType;
use Soap\WsdlReader\Metadata\Detector\ApacheMapDetector;
use Soap\WsdlReader\Metadata\Predicate\IsOfType;

final class ApacheMapReplacer implements TypeReplacer
{
    public function __invoke(XsdType $xsdType): XsdType
    {
        $check = new IsOfType(ApacheMapDetector::NAMESPACE_URI, 'Map');
        if (!$check($xsdType)) {
            return $xsdType;
        }

        return $xsdType->copy('array')
            ->withBaseType('array')
            ->withMeta(
                // Mark as simple to make sure no additional types are generated for this type.
                static fn (TypeMeta $meta): TypeMeta => $meta->withIsSimple(true)
            );
    }
}
