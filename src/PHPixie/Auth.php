<?php

namespace PHPixie;

class Auth
{
    protected $builder;
    
    public function __construct(
        $security,
        $configData,
        $repositoryRegistry = null,
        $contextContainer   = null
    )
    {
        $this->builder = $this->buildBuilder(
            $security,
            $configData,
            $repositoryRegistry,
            $contextContainer
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
    
    public function context()
    {
        return $this->builder->context();
    }
        
    public function builder()
    {
        return $this->builder;
    }
    
    protected function buildBuilder(
        $security,
        $configData,
        $repositoryRegistry,
        $contextContainer
    )
    {
        return new Auth\Builder(
            $security,
            $configData,
            $repositoryRegistry,
            $contextContainer
        );
    }
}