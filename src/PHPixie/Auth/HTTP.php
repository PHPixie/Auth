<?php

namespace PHPixie\Auth;

class HTTP
{
    public function cookie()
    {
        $context = $this->httpContext();
        $cookie = $cookies->get('remember_me');
        if($cookie === null) {
            return null;
        }
        
        $cookie = explode(':', $cookie);
        if(count($cookie) !== 2) {
            return null;
        }
        
        $token = $this->tokenHandler->get($cookie[0], $cookie[1]);
        $user = $this->userRepository->get($token->userId());
        
        $cookie = $token->series().':'.$token->passphrase;
        $cookies->set('remember_me', $cookie);
        
        return $user;
    }
    
    
}