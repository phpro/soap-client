<?php

namespace Phpro\SoapClient\CodeGenerator;

use Laminas\Code\Generator\FileGenerator;

/**
 * Interface GeneratorInterface
 *
 * @package Phpro\SoapClient\CodeGenerator
 */
interface GeneratorInterface
{
    // to ease X-OS compat, always use linux newlines
    const EOL = "\n";
    
    /**
     * @param FileGenerator $file
     * @param mixed         $model
     *
     * @return string
     */
    public function generate(FileGenerator $file, $model): string;
}
