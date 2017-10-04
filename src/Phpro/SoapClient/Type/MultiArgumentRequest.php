<?php

namespace Phpro\SoapClient\Type;

/**
 * Class MultiArgumentRequest
 *
 * @package Phpro\SoapClient\Type
 */
class MultiArgumentRequest implements MultiArgumentRequestInterface
{
    /**
     * @var array
     */
    private $arguments;

    /**
     * MultiArgumentRequest constructor.
     *
     * @param array $arguments
     */
    public function __construct(array $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }
}
