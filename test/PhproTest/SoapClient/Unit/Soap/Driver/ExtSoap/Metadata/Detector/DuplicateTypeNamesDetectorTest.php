<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Unit\Soap\Driver\ExtSoap\Metadata\Detector;

use Phpro\SoapClient\Soap\Driver\ExtSoap\Metadata\Detector\DuplicateTypeNamesDetector;
use Phpro\SoapClient\Soap\Engine\Metadata\Collection\TypeCollection;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\Type;
use Phpro\SoapClient\Soap\Engine\Metadata\Model\XsdType;
use PHPUnit\Framework\TestCase;

class DuplicateTypeNamesDetectorTest extends TestCase
{
    /** @test */
    public function it_can_detect_duplicate_type_names(): void
    {
        $detector = new DuplicateTypeNamesDetector();
        $types = new TypeCollection(
            new Type(XsdType::create('file'), []),
            new Type(XsdType::create('file'), []),
            new Type(XsdType::create('uppercased'), []),
            new Type(XsdType::create('Uppercased'), []),
            new Type(XsdType::create('with-specialchar'), []),
            new Type(XsdType::create('with*specialchar'), []),
            new Type(XsdType::create('not-duplicate'), []),
            new Type(XsdType::create('CASEISDIFFERENT'), []),
            new Type(XsdType::create('Case-is-different'), [])
        );

        $duplicates = $detector($types);

        self::assertSame(
            ['File', 'Uppercased', 'WithSpecialchar'],
            $duplicates
        );
    }
}
