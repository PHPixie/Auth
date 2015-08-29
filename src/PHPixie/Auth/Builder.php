<?php

namespace PHPixie\Auth;

class Builder
{
    protected $configData;
    protected $externalRepositoryRegistry;
    
    public function __construct($configData, $externalRepositoryRegistry = null)
    {
        $this->configData                 = $configData;
        $this->externalRepositoryRegistry = $externalRepositoryRegistry;
    }
    
    public function repositoryRegistry()
    {
        return new Repositories\Registry\Builder(
            $this->configData->slice('repositoories'),
            $this->externalRepositoryRegistry
        )
    }
    
    public function domains()
    {
        return new Domains(
            $this->configData->slice('domains'),
            $this->repositoryRegistry()
        );
    }
    
    public function domain($configData)
    {
        return new Domains\Domain($this, $configData);
    }
}