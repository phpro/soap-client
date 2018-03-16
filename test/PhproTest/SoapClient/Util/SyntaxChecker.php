<?php

namespace PhproTest\SoapClient\Util;

use Symfony\Component\Process\Process;

/**
 * Check string for valid PHP syntax
 *
 * Class SyntaxChecker
 * @package PhproTest\Util
 */
class SyntaxChecker
{
    /**
     * Validate php code in a string
     *
     * @param string $phpCode
     *
     * @return bool
     */
    public static function isValidPHP(string $phpCode): bool
    {
        // Remove docblocks
        $phpCode = preg_replace('/\/\*\*[.\s\*\w\@\$]*\/]*/m', '', $phpCode);

        // Check the code
        $process = new Process('echo $PHPCODE | php -l');
        $process->run(null, ['PHPCODE' => $phpCode]);

        return $process->isSuccessful();
    }
}
