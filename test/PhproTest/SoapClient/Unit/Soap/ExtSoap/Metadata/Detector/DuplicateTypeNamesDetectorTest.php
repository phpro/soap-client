<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Unit\Soap\ExtSoap\Metadata\Detector;

use Phpro\SoapClient\Soap\ExtSoap\Metadata\Detector\DuplicateTypeNamesDetector;
use PHPUnit\Framework\TestCase;
use Soap\Engine\Metadata\Collection\PropertyCollection;
use Soap\Engine\Metadata\Collection\TypeCollection;
use Soap\Engine\Metadata\Model\Type;
use Soap\Engine\Metadata\Model\XsdType;

class DuplicateTypeNamesDetectorTest extends TestCase
{
    /** @test */
    public function it_can_detect_duplicate_type_names(): void
    {
        $detector = new DuplicateTypeNamesDetector();
        $types = new TypeCollection(
            new Type(XsdType::create('file'), new PropertyCollection()),
            new Type(XsdType::create('file'), new PropertyCollection()),
            new Type(XsdType::create('uppercased'), new PropertyCollection()),
            new Type(XsdType::create('Uppercased'), new PropertyCollection()),
            new Type(XsdType::create('with-specialchar'), new PropertyCollection()),
            new Type(XsdType::create('with*specialchar'), new PropertyCollection()),
            new Type(XsdType::create('not-duplicate'), new PropertyCollection()),
            new Type(XsdType::create('CASEISDIFFERENT'), new PropertyCollection()),
            new Type(XsdType::create('Case-is-different'), new PropertyCollection())
        );

        $duplicates = $detector($types);

        self::assertSame(
            ['File', 'Uppercased', 'WithSpecialchar'],
            $duplicates
        );
    }
}
