<?php

namespace PHPixie\Auth\Context\Container;

class Implementation implements \PHPixie\Auth\Context\Container
{
    protected $context;
    
    public function __construct($context = null)
    {
        $this->context = $context;
    }
    
    public function authContext()
    {
        return $this->context;
    }
}