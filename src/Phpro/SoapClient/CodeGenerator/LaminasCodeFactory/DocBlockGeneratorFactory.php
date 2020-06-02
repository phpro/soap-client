<?php declare(strict_types=1);

namespace Phpro\SoapClient\CodeGenerator\LaminasCodeFactory;

use Laminas\Code\Generator\DocBlockGenerator;

final class DocBlockGeneratorFactory
{
    public static function fromArray(array $data): DocBlockGenerator
    {
        $generator = DocBlockGenerator::fromArray($data);
        $generator->setWordWrap(false);
        return $generator;
    }
}
