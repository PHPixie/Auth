<?php

namespace PHPixie\Auth\Handlers;

abstract class Handler
{
    protected $domain;
    
    public function __construct($domain)
    {
        $this->domain = $domain;
    }
}