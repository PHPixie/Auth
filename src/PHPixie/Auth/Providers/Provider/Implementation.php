<?php

namespace PHPixie\Auth\Providers\Provider;

abstract class Implementation implements \PHPixie\Auth\Providers\Provider
{
    protected $domain;
    protected $name;
    protected $configData;
    
    public function __construct($domain, $name, $configData)
    {
        $this->domain     = $domain;
        $this->name       = $name;
        $this->configData = $configData;
    }
    
    public function name()
    {
        return $this->name;
    }
    
    protected function repository()
    {
        return $this->domain->repository();
    }
}