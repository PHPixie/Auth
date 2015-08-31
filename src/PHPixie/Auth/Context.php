<?php

namespace PHPixie\Auth;

class Context
{
    protected $users = array();
    protected $usedProviders = array();
    
    public function setUser($domain, $user, $providerName = null)
    {
        $this->users[$domain]         = $user;
        $this->usedProviders[$domain] = $providerName;
    }
    
    public function unsetUser($domain = 'default')
    {
        unset($this->users[$domain]);
        unset($this->usedProviders[$domain]);
    }
    
    public function user($domain = 'default')
    {
        if(array_key_exists($domain, $this->users)) {
            return $this->users[$domain];
        }
        
        return null;
    }
    
    public function usedProvider($domain = 'default')
    {
        return $this->usedProviders[$domain];
    }
}