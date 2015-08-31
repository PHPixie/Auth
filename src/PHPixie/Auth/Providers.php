<?php

namespace PHPixie\Auth;

class Providers
{
    protected $handlers;
    protected $httpContextContainer;
    
    public function __construct($handlers, $httpContextContainer = null)
    {
        $this->handlers             = $handlers;
        $this->httpContextContainer = $httpContextContainer;
    }
    
    public function password($domain, $name, $configData)
    {
        return new Providers\Type\Password(
            $this->handlers->password(),
            $domain,
            $name,
            $configData
        );
    }
    
    public function cookie($domain, $name, $configData)
    {
        return new Providers\Type\Cookie(
            $this->handlers->tokens(),
            $this->httpContextContainer,
            $domain,
            $name,
            $configData
        );
    }
    
    public function session($domain, $name, $configData)
    {
        return new Providers\Type\Password(
            $this->httpContextContainer,
            $domain,
            $name,
            $configData
        );
    }
    
    public function buildFromConfig($domain, $name, $configData)
    {
        $method = $configData->getRequired('type');
        return $this->$method($domain, $name, $configData);
    }
}