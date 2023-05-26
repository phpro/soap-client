<?php

namespace Phpro\SoapClient\Type;

/**
 * @template T
 */
class MixedResult implements ResultInterface
{
    /**
     * @var T
     */
    private $result;

    /**
     * @param T $result
     */
    public function __construct($result)
    {
        $this->result = $result;
    }

    /**
     * @return T
     */
    public function getResult()
    {
        return $this->result;
    }
}
