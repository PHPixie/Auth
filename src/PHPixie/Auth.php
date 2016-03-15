<?php

namespace PHPixie;

use PHPixie\Auth\Domains\Domain;

class Auth
{
    protected $builder;
    
    public function __construct(
        $configData,
        $repositoryRegistry = null,
        $providerBuilders   = array(),
        $contextContainer   = null
    )
    {
        $this->builder = $this->buildBuilder(
            $configData,
            $repositoryRegistry,
            $providerBuilders,
            $contextContainer
        );
    }
    
    public function domains()
    {
        return $this->builder->domains();
    }

    /**
     * @param string $name
     * @return Domain
     */
    public function domain($name = 'default')
    {
        return $this->builder->domains()->get($name);
    }
    
    public function context()
    {
        return $this->builder->context();
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
        $configData,
        $repositoryRegistry,
        $providerBuilders,
        $contextContainer
    )
    {
        return new Auth\Builder(
            $configData,
            $repositoryRegistry,
            $providerBuilders,
            $contextContainer
        );
    }
}