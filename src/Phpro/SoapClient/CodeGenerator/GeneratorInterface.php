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
    /**
     * @param FileGenerator $file
     * @param mixed         $model
     *
     * @return string
     */
    public function generate(FileGenerator $file, $model): string;
}
