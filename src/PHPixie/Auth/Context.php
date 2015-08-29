<?php

namespace PHPixie\Auth;

class Context
{
    protected $domains;
    
    public function __construct($domains)
    {
        $this->domains = $domains;
    }
    
    public function domains($domains)
}