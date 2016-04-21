<?php

namespace MyNamespace;

class MyService
{
    /**
     * @var string
     */
    protected $myField;

    /**
     * @return string
     */
    public function getMyField()
    {
        return $this->myField;
    }
}
