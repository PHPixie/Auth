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
        $this->tokens               = $tokens;
        $this->httpContextContainer = $httpContextContainer;
        
        parent::__construct($domain, $name, $configData);
    }
    
    public function check()
    {
        $encodedToken = $this->getCookie();
        
        if($encodedToken === null) {
            return null;
        }
        
        $token = $this->tokenHandler()->getByString($encodedToken);
        
        if($token === null) {
            $this->unsetCookie();
            return null;
        }
        
        $userId = $token->userId();
        $user = $this->repository()->getById($userId);
        
        if($user === null) {
            $this->removeToken($encodedToken);
            $this->unsetCookie();
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
        $encodedToken = $this->getCookie();
        
        if($encodedToken === null) {
            return;
        }
        
        $this->unsetCookie();
        $this->removeToken($encodedToken);
    }
    
    protected function setCookie($token)
    {
        $cookies = $this->cookies();
        $cookies->set(
            $this->cookieName(),
            $token->string(),
            $token->expires() - time()
        );
    }
    
    
    protected function getCookie()
    {
        $this->cookieName();
        return $this->cookies()->get($this->cookieName);
    }

    protected function unsetCookie()
    {
        $this->cookies()->remove($this->cookieName());
    }
    
    protected function removeToken($encodedToken)
    {
        $this->tokenHandler()->removeByString($encodedToken);
    }

    protected function cookieName()
    {
        if($this->cookieName === null) {
            $defaultKey = $this->domain->name().'Token';
            $this->cookieName = $this->configData->get('cookie', $defaultKey);
        }
        
        return $this->cookieName;
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