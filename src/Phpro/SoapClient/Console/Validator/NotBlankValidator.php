<?php

namespace Phpro\SoapClient\Console\Validator;

use Symfony\Component\Console\Exception\LogicException;
use function is_array;
use function is_bool;

/**
 * Class NotBlankValidator
 *
 * A validator to restore the behaviour of required questions
 *
 * @package Phpro\SoapClient\Console\Validator
 */
class NotBlankValidator
{
    public function __invoke($value)
    {
        if (!is_array($value) && !is_bool($value) && 0 === \strlen($value)) {
            throw new LogicException('A value is required.');
        }

        return $value;
    }
}
