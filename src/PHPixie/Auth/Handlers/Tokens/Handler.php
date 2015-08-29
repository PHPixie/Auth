<?php

namespace PHPixie\Auth\Persistance;

class Handler
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
            $challenge
        );
        
        $tokenStorage->insert($token, $this->expires($lifetime));
        return $this->tokenString($series, $passphrase);
    }
    
    public function validate($encodedToken)
    {
        $token = $this->decodeToken($encodedToken);
        
        if($token === null) {
            return null;
        }
        
        list($series, $passphrase) = $token;
        
        $token = $tokenStorage->get($series);
        
        if($token === null) {
            return null;
        }
        
        if($this->challenge($series, $passphrase) !== $token->challenge()) {
            $this->tokenStorage->remove($series);
            return null;
        }
        
        return $token->userId();
    }
    
    public function refresh($encodedToken, $lifetime)
    {
        list($series, $passphrase) = $this->decodeToken($encodedToken);
        
        $passphrase = $random->string(30);
        $challenge  = $this->challenge($series, $passphrase);
        
        $token = $this->tokenStorage->update($series, $challenge, $this->expires($lifetime));
        return $this->encodeToken($series, $passphrase);
    }
    
    public function remove($token)
    {
        list($series, $passphrase) = $this->decodeToken($encodedToken);
        $this->tokenStorage->removeSeries($series);
    }
    
    protected function challenge($series, $passphrase)
    {
        return md5($series.$passphrase);
    }
    
    protected function encodeToken($series, $passphrase)
    {
        return $series.':'.$passphrase;
    }
    
    protected function decodeToken($token)
    {
        $token = explode(':', $token);
        if(count($token) !== 2) {
            return null;
        }
        
        return $token;
    }
    
}