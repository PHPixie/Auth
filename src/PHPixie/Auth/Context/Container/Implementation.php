<?php

namespace PHPixie\Auth\Context\Container;

class Implementation implements \PHPixie\Auth\Context\Container
{
    /**
     * @var \PHPixie\Auth\Context
     */
    protected $context;

    /**
     * @param \PHPixie\Auth\Context $context
     */
    public function __construct($context = null)
    {
        $this->context = $context;
    }

    /**
     * @return \PHPixie\Auth\Context
     */
    public function authContext()
    {
        return $this->context;
    }
}