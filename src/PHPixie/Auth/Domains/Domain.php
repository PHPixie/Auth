<?php

namespace PHPixie\Auth\Domains;

class Domain
{
    protected $builder;
    protected $configData;
    
    protected $handlers;
    
    public function __construct($builder, $configData)
    {
        $this->builder    = $builder;
        $this->configData = $configData;
    }
    
    public function repository()
    {
        if($this->repository === null) {
            $repositoryConfig = $this->configData->slice('repository');
            $this->repository = $this->buildRepository($repositoryConfig);
        }
        
        return $this->repository;
    }
    
    public function handler($name)
    {
        $this->requireHandlers();
        return $this->handlers[$name];
    }
    
    public function handlers()
    {
        $this->requireHandlers();
        return $this->handlers;
    }
    
    protected function requireHandlers()
    {
        if($this->handlers !== null) {
            return;
        }
        
        $handlers = array();
        foreach($this->configData->keys() as $name) {
            $domainConfig = $this->configData->slice($name);
            $handlers[$name] = $this->builder->buildDomain($domainConfig);
        }
        
        $this->handlers = $handlers;
    }
}