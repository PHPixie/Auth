<?php

namespace PHPixie\Auth\Providers\Type;

class Session extends    \PHPixie\Auth\Providers\Provider\Implementation
              implements \PHPixie\Auth\Providers\Provider\Persistent
{
    protected $httpContextContainer;
    protected $sessionKey;
    
    public function __construct($httpContextContainer, $domain, $name, $configData)
    {
        $this->httpContextContainer = $httpContextContainer;
        
        $defaultKey = $domain->name().'UserId';
        $this->sessionKey = $configData->get('key', $defaultKey);
        
        parent::__construct($domain, $name, $configData);
    }
    
    public function check()
    {
        $session = $this->session();
        $userId = $session->get($this->sessionKey);
        if($userId === null) {
            return null;
        }
        
        $user = $this->repository()->getById($userId);
        if($user === null) {
            $this->forget();
            return null;
        }
        
        $this->domain->setUser($user, $this->name);
        return $user;
    }
    
    public function persist()
    {
        $user = $this->domain->requireUser();
        $this->session()->set($this->sessionKey, $user->id());
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