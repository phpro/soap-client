<?php

declare(strict_types=1);

namespace Phpro\SoapClient\CodeGenerator\Context;

use Laminas\Code\Generator\FileGenerator;

final readonly class FileContext implements ContextInterface
{
    public function __construct(
        private FileGenerator $fileGenerator
    ) {
    }

    public function getFileGenerator(): FileGenerator
    {
        return $this->fileGenerator;
    }
}
