<?php

namespace PHPixie\Auth\Providers;

class Session extends    \PHPixie\Auth\Handlers\Handler
              implements \PHPixie\Auth\Handlers\Handler\Persistent
{
    public function check()
    {
        $session = $this->httpContext()->session();
        $userId = $session->get($this->sessionIdKey);
        if($userId === null) {
            return false;
        }
        
        $user = $this->userRepository->get($userId);
        if($user === null) {
            return false;
        }
        
        $this->authContext->setUser($user);
    }
    
    public function persist()
    {
        $userId = $user->id();
        $session->set($this->sessionIdKey, $userId);
    }
    
    protected function session()
    {
        $httpContext = $this->httpContextContainer->httpContext();
        return $httpContext->session();
    }
}