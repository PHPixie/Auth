<?php

namespace PHPixie;

class Auth
{
    protected $builder;
    
    public function __construct(
        $database,
        $externalRepositories = null,
        $httpContextContainer = null,
        $authContextContainer = null
    )
    {
        $this->builder = $this->buildBuilder(
            $database,
            $externalRepositories,
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
        
    public function builder()
    {
        return $this->builder;
    }
    
    protected function buildBuilder(
        $database,
        $externalRepositories,
        $httpContextContainer,
        $authContextContainer
    )
    {
        return new Auth\Builder(
            $database,
            $externalRepositories,
            $httpContextContainer,
            $authContextContainer
        );
    }
}