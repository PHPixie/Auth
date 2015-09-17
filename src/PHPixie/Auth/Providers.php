<?php

namespace PHPixie\Auth;

class Providers
{
    protected $builders;
    
    public function __construct($builders = array())
    {
        foreach($builders as $builder) {
            $this->builders[$builder->name()] = $builder;
        }
    }
    
    public function buildFromConfig($domain, $name, $configData)
    {
        $type = $configData->getRequired('type');
        
        $split = explode('.', $type, 2);
        if(count($split) === 2) {
            $builder = $this->builders[$split[0]];
            return $builder->buildProvider(
                $split[1],
                $domain,
                $name,
                $configData
            );
        }
        
        throw new \PHPixie\Auth\Exception("Provider '$type' does not exist");
    }
}