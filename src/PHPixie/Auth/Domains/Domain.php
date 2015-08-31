<?php

namespace PHPixie\Auth\Domains;

class Domain
{
    protected $builder;
    protected $name;
    protected $configData;
    
    protected $providers = array();
    
    public function __construct($builder, $name, $configData)
    {
        $this->builder    = $builder;
        $this->name       = $name;
        $this->configData = $configData;
    }
    
    public function repository()
    {
        if($this->repository === null) {
            $repositoryName = $this->configData->get('repository', 'default');
            $repositories = $this->builder->repositories();
            $this->repository = $repositories->get($repositoryName);
        }
        
        return $this->repository;
    }
    
    public function provider($name)
    {
        $this->requireHandlers();
        return $this->providers[$name];
    }
    
    public function providers()
    {
        $this->requireHandlers();
        return $this->providers;
    }
    
    public function check()
    {
        foreach($this->peoviders() as $provider) {
            if($provider instanceof \PHPixie\Auth\Providers\Provider\Autologin) {
                if($provider->check() !== null) {
                    break;
                }
            }
        }
        
        return null;
    }
    
    public function clearUser()
    {
        $this->authContext()->clearUser($this->name);
    }
    
    public function setUser($user, $providerName)
    {
        $this->authContext()->setUser($this->name, $user, $providerName);
    }
    
    protected function authContext()
    {
        return $this->builder->context();
    }
    
    protected function requireProviders()
    {
        if($this->providers !== null) {
            return;
        }
        
        $providerBuilder = $this->builder->providers();
        
        $providers = array();
        foreach($this->configData->keys() as $name) {
            $providerConfig = $this->configData->slice($name);
            $providers[$name] = $providerBuilder->buildFromConfig($providerConfig);
        }
        
        $this->providers = $providers;
    }
}