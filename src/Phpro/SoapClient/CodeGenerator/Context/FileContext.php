<?php

declare(strict_types=1);

namespace Phpro\SoapClient\CodeGenerator\Context;

use Zend\Code\Generator\FileGenerator;

class FileContext implements ContextInterface
{
    private $fileGenerator;

    public function __construct(FileGenerator $fileGenerator)
    {
        $this->fileGenerator = $fileGenerator;
    }

    /**
     * @return FileGenerator
     */
    public function getFileGenerator(): FileGenerator
    {
        return $this->fileGenerator;
    }
}
