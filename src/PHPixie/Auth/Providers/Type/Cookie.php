<?php

namespace PHPixie\Auth\Providers;

class Session
{
    protected $domain;
    
    public function __construct($domain)
    {
        $this->domain = $domain;
    }
    
    public function check()
    {
        $cookies = $this->cookies();
        $cookie = $cookies->get('remember_me');
        if($cookie === null) {
            return null;
        }
        
        $userId = $this->tokenHandler->get($cookie);
        $user = $this->userRepository->get($userId);
        
        $cookie = $this->tokenHandler->refresh($cookie);
        
        return $user;
    }
    
    public function persist()
    {
        $userId = $user->id();
        $session->set($this->sessionIdKey, $userId);
    }
    
    protected function cookies()
    {
        $httpContext = $this->httpContextContainer->httpContext();
        return $httpContext->cookies();
    }
}