<?php

namespace PHPixie\Auth;

class Builder
{
    protected $security;
    protected $configData;
    protected $repositoryRegistry;
    protected $contextContainer;
    
    protected $instances = array();
    
    public function __construct(
        $security,
        $configData,
        $repositoryRegistry   = null,
        $contextContainer = null
    )
    {
        $this->security           = $security;
        $this->configData         = $configData;
        $this->repositoryRegistry = $repositoryRegistry;
        $this->contextContainer   = $contextContainer;
    }
    
    public function domains()
    {
        return $this->instance('domains');
    }

    public function providers()
    {
        return $this->instance('providers');
    }
    
    public function repositories()
    {
        return $this->instance('repositories');
    }
    
    public function contextContainer()
    {
        if($this->contextContainer === null) {
            $this->contextContainer = $this->buildContextContainer();
        }
        
        return $this->contextContainer;
    }
    
    public function context()
    {
        return $this->contextContainer()->authContext();
    }
    
    public function buildContext()
    {
        return new Context();
    }
    
    public function buildContextContainer($context = null)
    {
        return new Context\Container\Implementation($context);
    }
    
    public function buildDomain($name, $configData)
    {
        return new Domains\Domain($this, $name, $configData);
    }
    
    protected function instance($name)
    {
        if(!array_key_exists($name, $this->instances)) {
            $method = 'build'.ucfirst($name);
            $this->instances[$name] = $this->$method();
        }
        
        return $this->instances[$name];
    }
    
    protected function buildDomains()
    {
        return new Domains(
            $this,
            $this->configData->slice('domains')
        );
    }
    
    protected function buildProviders()
    {
        return new Providers(
            $this->security
        );
    }
    
    protected function buildRepositories()
    {
        return new Repositories(
            $this->repositoryRegistry
        );
    }
}