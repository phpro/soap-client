<?php declare(strict_types=1);

namespace Phpro\SoapClient\CodeGenerator\ZendCodeFactory;

use Zend\Code\Generator\DocBlockGenerator;

final class DocBlockGeneratorFactory
{
    public static function fromArray(array $data): DocBlockGenerator
    {
        $generator = DocBlockGenerator::fromArray($data);
        $generator->setWordWrap(false);
        return $generator;
    }
}
