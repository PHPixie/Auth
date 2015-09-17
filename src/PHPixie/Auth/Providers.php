<?php

namespace PHPixie\Auth;

class Providers
{
    protected $security;
    protected $httpContextContainer;
    protected $builders;
    
    protected $types = array(
        'password'
    );
    
    public function __construct($security, $httpContextContainer = null, $builders = array())
    {
        $this->security             = $security;
        $this->httpContextContainer = $httpContextContainer;
        foreach($builders as $builder) {
            $this->builders[$builder->name()] = $builder;
        }
    }
    
    public function password($domain, $name, $configData)
    {
        return new Providers\Type\Password(
            $this->security->password(),
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