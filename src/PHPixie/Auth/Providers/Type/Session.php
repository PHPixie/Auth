<?php

namespace PHPixie\Auth\Providers;

class Session extends    \PHPixie\Auth\Handlers\Handler
              implements \PHPixie\Auth\Handlers\Handler\Persistent
{
    public function check()
    {
        $session = $this->session();
        $userId = $session->get($this->sessionKey);
        if($userId === null) {
            return null;
        }
        
        $user = $this->userRepository->get($userId);
        if($user === null) {
            return null;
        }
        
        $this->domain->setUser($user, $this->name);
        return $user;
    }
    
    public function persist()
    {
        $user = $this->domain->requireUser();
        $session->set($this->sessionKey, $user->id());
    }
    
    public function forget()
    {
        $this->session()->remove($this->sessionKey);
    }
    
    protected function session()
    {
        $httpContext = $this->httpContextContainer->httpContext();
        return $httpContext->session();
    }
}