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
            $repositoryName = $this->configData->getRequired('repository');
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
    
    public function checkUser()
    {
        $this->unsetUser();
        
        $user = null;
        foreach($this->providers() as $provider) {
            if($provider instanceof \PHPixie\Auth\Providers\Provider\Autologin) {
                $user = $provider->check();
                if($user !== null) {
                    break;
                }
            }
        }
        
        return $user;
    }
    
    public function forgetUser()
    {
        $this->unsetUser();
        
        foreach($this->providers() as $provider) {
            if($provider instanceof \PHPixie\Auth\Providers\Provider\Persistent) {
                $provider->forget();
            }
        }
    }
    
    public function unsetUser()
    {
        $this->context()->unsetUser($this->name);
    }
    
    public function setUser($user, $providerName)
    {
        $this->context()->setUser($user, $this->name, $providerName);
    }
    
    public function user()
    {
        return $this->context()->user($this->name);
    }
    
    public function requireUser()
    {
        $user = $this->user();
        if($user === null) {
            throw new \PHPixie\Auth\Exception("No user set");
        }
        
        return $user;
    }
    
    public function name()
    {
        return $this->name;
    }
    
    protected function context()
    {
        $contextContainer = $this->builder->contextContainer();
        return $contextContainer->authContext();
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