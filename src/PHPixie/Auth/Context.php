<?php

namespace PHPixie\Auth;

class Context
{
    /**
     * @var \PHPixie\AuthLogin\Repository\User[]
     */
    protected $users = array();

    /**
     * @var array
     */
    protected $usedProviders = array();

    /**
     * @param \PHPixie\AuthLogin\Repository\User $user
     * @param string                             $domain
     * @param string                             $providerName
     */
    public function setUser($user, $domain = 'default', $providerName = null)
    {
        $this->users[$domain]         = $user;
        $this->usedProviders[$domain] = $providerName;
    }

    /**
     * @param string $domain
     */
    public function unsetUser($domain = 'default')
    {
        unset($this->users[$domain]);
        unset($this->usedProviders[$domain]);
    }

    /**
     * @param string $domain
     * @return \PHPixie\AuthLogin\Repository\User
     */
    public function user($domain = 'default')
    {
        if(array_key_exists($domain, $this->users)) {
            return $this->users[$domain];
        }
        
        return null;
    }

    /**
     * @param string $domain
     * @return string
     */
    public function usedProvider($domain = 'default')
    {
        if(array_key_exists($domain, $this->usedProviders)) {
            return $this->usedProviders[$domain];
        }
        
        return null;
    }
}