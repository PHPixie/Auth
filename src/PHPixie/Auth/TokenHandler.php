<?php

namespace PHPixie\Auth;

class TokenHandler
{
    protected $random;
    protected $tokenStorage;
    
    public function __construct($random, $tokenStorage)
    {
        $this->random       = $random;
        $this->tokenStorage = $tokenStorage;
    }
    
    public function create($user, $lifetime)
    {
        $series     = $random->string(30);
        $passphrase = $random->string(30);
        $challenge  = $this->challenge($series, $passphrase);
        
        $token = $this->token(
            $user->id(),
            $series,
            $challenge,
            $passphrase
        );
        
        $tokenStorage->insert($token, $this->lifetime);
        return $token;
    }
    
    public function validate($series, $passphrase)
    {
        $token = $tokenStorage->get($series);
        
        if($token === null) {
            return null;
        }
        
        if($this->challenge($series, $passphrase) !== $token->challenge()) {
            $tokenStorage->remove($token->series);
            return null;
        }
        
        return $token;
    }
    
    public function regenerate($token)
    {
        $token = explode(':', $token);
        
        $passphrase = $random->string(30);
        $challenge  = $this->challenge($series, $passphrase);
        
        $token = $this->token(
            $user->id(),
            $token[0],
            $challenge,
            $passphrase
        );
        
        $token = $this->tokenStorage->update($token, $this->lifetime);
        return $token;
    }
    
    protected function challenge($series, $passphrase)
    {
        return md5($series.$passphrase);
    }
}