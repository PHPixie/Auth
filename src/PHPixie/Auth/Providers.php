<?php

namespace PHPixie\Auth;

class Providers
{
    protected $handlers;
    protected $httpContextContainer;
    protected $builders;
    
    protected $types = array(
        'password',
        'cookie',
        'session'
    );
    
    public function __construct($handlers, $httpContextContainer = null, $builders = array())
    {
        $this->handlers             = $handlers;
        $this->httpContextContainer = $httpContextContainer;
        foreach($builders as $builder) {
            $this->builders[$builder->name()] = $builder;
        }
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
        return new Providers\Type\Session(
            $this->httpContextContainer,
            $domain,
            $name,
            $configData
        );
    }
    
    public function buildFromConfig($domain, $name, $configData)
    {
        $type = $configData->getRequired('type');
        if(in_array($type, $this->types)) {
            return $this->$type($domain, $name, $configData);
        }
        
        $split = explode('.', $type, 2);
        if(count($split) === 2) {
            $builder = $this->builders[$split[0]];
            return $builder->buildFromConfig(
                $split[1],
                $domain,
                $name,
                $configData
            );
        }
        
        throw new \PHPixie\Auth\Exception("Provider '$type' does not exist");
    }
}