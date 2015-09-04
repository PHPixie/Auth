<?php

namespace PHPixie\Auth\Providers\Type;

class Cookie extends    \PHPixie\Auth\Providers\Provider\Implementation
             implements \PHPixie\Auth\Providers\Provider\Persistent
{
    protected $tokens;
    protected $httpContextContainer;
    protected $cookieName;
    protected $tokenHandler;
    
    public function __construct($tokens, $httpContextContainer, $domain, $name, $configData)
    {
        $this->tokens              = $tokens;
        $this->httpContextContainer = $httpContextContainer;
        $this->cookieName = $configData->get('cookie', 'auth');
        
        parent::__construct($domain, $name, $configData);
    }
    
    public function check()
    {
        $cookies = $this->cookies();
        $encodedToken = $cookies->get($this->cookieName);
        
        if($encodedToken === null) {
            return null;
        }
        
        $token = $this->tokenHandler()->getByString($encodedToken);
        
        if($token === null) {
            $this->forget();
            return null;
        }
        
        $userId = $token->userId();
        $user = $this->repository()->getById($userId);
        
        if($user === null) {
            $this->forget();
            return null;
        }
        
        if($this->configData->get('refresh', true)) {
            $token = $this->tokenHandler->refresh($token);
            $this->setCookie($token);
        }
        
        $persistProviders = $this->configData->get('persistProviders', array());
        
        foreach($persistProviders as $providerName) {
            $this->domain->provider($providerName)->persist();
        }
        
        $this->domain->setUser($user, $this->name);
        return $user;
    }
    
    public function persist($lifetime = null)
    {
        if($lifetime === null) {
            $lifetime = $this->configData->get('defaultLifetime', 14*24*3600);
        }
        
        $user = $this->domain->requireUser();
        $token = $this->tokenHandler()->create($user, $lifetime);
        $this->setCookie($token);
    }
    
    public function forget()
    {
        $this->cookies()->remove($this->cookieName);
    }
    
    protected function setCookie($token)
    {
        $cookies = $this->cookies();
        $cookies->set(
            $this->cookieName,
            $token->string(),
            $token->expires() - time()
        );
    }
    
    protected function tokenHandler()
    {
        if($this->tokenHandler === null) {
            $configData = $this->configData->slice('tokens');
            $this->tokenHandler = $this->tokens->handler($configData);
        }
        
        return $this->tokenHandler;
    }
    
    protected function cookies()
    {
        $httpContext = $this->httpContextContainer->httpContext();
        return $httpContext->cookies();
    }
}