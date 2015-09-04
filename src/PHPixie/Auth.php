<?php

namespace PHPixie;

class Auth
{
    protected $builder;
    
    public function __construct(
        $database,
        $configData,
        $repositoryRegistry   = null,
        $httpContextContainer = null,
        $authContextContainer = null
    )
    {
        $this->builder = $this->buildBuilder(
            $database,
            $configData,
            $repositoryRegistry,
            $httpContextContainer,
            $authContextContainer
        );
    }
    
    public function domains()
    {
        return $this->builder->domains();
    }
    
    public function domain($name = 'default')
    {
        return $this->builder->domains()->get($name);
    }
    
    public function buildContext()
    {
        return $this->builder->buildContext();
    }
        
    public function builder()
    {
        return $this->builder;
    }
    
    protected function buildBuilder(
        $database,
        $configData,
        $repositoryRegistry,
        $httpContextContainer,
        $authContextContainer
    )
    {
        return new Auth\Builder(
            $database,
            $configData,
            $repositoryRegistry,
            $httpContextContainer,
            $authContextContainer
        );
    }
}