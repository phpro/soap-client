<?php

namespace MyNamespace;

class MyService
{
    /**
     * @var string
     */
    protected $myField;

    /**
     * @var string
     */
    protected $newField;

    /**
     * @return string
     */
    public function getMyField()
    {
        return $this->myField;
    }
}
