<?php

namespace PHPixie\Auth\Providers\Builder;

abstract class Implementation implements \PHPixie\Auth\Providers\Builder
{
    public function buildProvider($type, $domain, $name, $configData)
    {
        $method = 'build'.ucfirst($type).'Provider';
        return $this->$method($domain, $name, $configData);
    }
    
    abstract public function name();
}