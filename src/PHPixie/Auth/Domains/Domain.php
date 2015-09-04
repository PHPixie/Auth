<?php

namespace PHPixie\Auth\Domains;

class Domain
{
    protected $builder;
    protected $name;
    protected $configData;
    
    protected $repository;
    protected $providers = null;
    
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
        $this->requireProviders();
        return $this->providers[$name];
    }
    
    public function providers()
    {
        $this->requireProviders();
        return $this->providers;
    }
    
    public function check()
    {
        foreach($this->providers() as $provider) {
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
        $this->authContext()->unsetUser($this->name);
        foreach($this->providers() as $provider) {
            if($provider instanceof \PHPixie\Auth\Providers\Provider\Autologin) {
                $provider->forget();
            }
        }
    }
    
    public function setUser($user, $providerName)
    {
        $this->authContext()->setUser($this->name, $user, $providerName);
    }
    
    public function user()
    {
        return $this->authContext()->user($this->name);
    }
    
    public function requireUser()
    {
        $user = $this->user();
        if($user === null) {
            throw new \PHPixie\Auth\Exception("No user set");
        }
        
        return $user;
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
        $providersConfig = $this->configData->slice('providers');
        foreach($providersConfig->keys() as $name) {
            $providerConfig = $providersConfig->slice($name);
            $providers[$name] = $providerBuilder->buildFromConfig(
                $this,
                $name,
                $providerConfig
            );
        }
        
        $this->providers = $providers;
    }
}