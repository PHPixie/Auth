<?php

namespace PHPixie\Auth\Providers\Type;

class Password extends \PHPixie\Auth\Providers\Provider\Implementation
{
    protected $passwordHandler;
    
    public function __construct($passwordHandler, $domain, $name, $configData)
    {
        parent::__construct($domain, $name, $configData);
        $this->passwordHandler = $passwordHandler;
    }
    
    public function hash($password)
    {
        return $this->passwordHandler->hash($password);
    }
    
    public function login($login, $password)
    {
        $user = $this->repository()->getByLogin($login);
        
        if($user === null) {
            return null;
        }
        
        $hash = $user->passwordHash();
        if(!$this->passwordHandler->verify($password, $hash)) {
            return null;
        }
        
        $this->domain->setUser($user, $this->name);
        
        $persistProviders = $this->configData->get('persistProviders', array());
        
        foreach($persistProviders as $providerName) {
            $this->domain->provider($providerName)->persist();
        }
        
        return $user;
    }
}