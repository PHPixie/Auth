<?php

namespace PHPixie\Auth\Handlers\Handler;

abstract class Implementation implements \PHPixie\Auth\Handlers\Handler
{
    protected $domain;
    protected $name;
    
    public function __construct($domain, $name)
    {
        $this->domain = $domain;
        $this->name   = $name;
    }
    
    public function name()
    {
        return $this->name;
    }
}