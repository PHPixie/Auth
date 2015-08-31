<?php

namespace PHPixie\Auth;

class Builder
{
    protected $database;
    protected $httpContextContainer;
    protected $authContextContainer;
    
    protected $instances = array();
    
    public function __construct(
        $database,
        $externalRepositories = null,
        $httpContextContainer = null,
        $authContextContainer = null
    )
    {
        $this->database             = $database;
        $this->repositoryRegistry   = $repositoryRegistry;
        $this->httpContextContainer = $httpContextContainer;
        $this->authContextContainer = $authContextContainer;
    }
    
    public function domains()
    {
        return $this->instance('domains');
    }
    
    public function handlers()
    {
        return $this->instance('handlers');
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
        return $this->contextContainer()->context();
    }
    
    public function buildContext()
    {
        return new Context();
    }
    
    public function buildContextContainer()
    {
        return new Context\Container\Implementation();
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
    
    protected function buildHandlers()
    {
        return new Handlers(
            $this->database
        );
    }
    
    protected function buildProviders()
    {
        return new Providers(
            $this->handlers(),
            $this->httpContextContainer
        );
    }
    
    protected function buildRepositories()
    {
        return new Repositories(
            $this->repositoryRegistry
        );
    }
}