<?php

namespace PHPixie\Auth\Domains;

class Domain
{
    /**
     * @var \PHPixie\Auth\Builder
     */
    protected $builder;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \PHPixie\Slice\Type\ArrayData
     */
    protected $configData;

    /**
     * @var \PHPixie\AuthLogin\Repository
     */
    protected $repository;

    /**
     * @var \PHPixie\Auth\Providers\Provider[]
     */
    protected $providers = null;

    /**
     * @param \PHPixie\Auth\Builder         $builder
     * @param string                        $name
     * @param \PHPixie\Slice\Type\ArrayData $configData
     */
    public function __construct($builder, $name, $configData)
    {
        $this->builder    = $builder;
        $this->name       = $name;
        $this->configData = $configData;
    }

    /**
     * @return \PHPixie\AuthLogin\Repository
     * @throws \PHPixie\Auth\Exception
     */
    public function repository()
    {
        if($this->repository === null) {
            $repositoryName = $this->configData->getRequired('repository');
            $repositories = $this->builder->repositories();
            $this->repository = $repositories->get($repositoryName);
        }
        
        return $this->repository;
    }

    /**
     * @param string $name
     * @return \PHPixie\Auth\Providers\Provider
     */
    public function provider($name)
    {
        $this->requireProviders();
        return $this->providers[$name];
    }

    /**
     * @return \PHPixie\Auth\Providers\Provider[]
     */
    public function providers()
    {
        $this->requireProviders();
        return $this->providers;
    }

    /**
     * @return \PHPixie\AuthLogin\Repository\User
     */
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

    /**
     * @param \PHPixie\AuthLogin\Repository\User $user
     * @param string $providerName
     */
    public function setUser($user, $providerName)
    {
        $this->context()->setUser($user, $this->name, $providerName);
    }

    /**
     * @return \PHPixie\AuthLogin\Repository\User
     */
    public function user()
    {
        return $this->context()->user($this->name);
    }

    /**
     * @return \PHPixie\AuthLogin\Repository\User
     * @throws \PHPixie\Auth\Exception
     */
    public function requireUser()
    {
        $user = $this->user();
        if($user === null) {
            throw new \PHPixie\Auth\Exception("No user set");
        }
        
        return $user;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @return \PHPixie\Auth\Context
     */
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