<?php

namespace PHPixie\Auth\Tokens;

class Handler
{
    protected $random;
    protected $configData;
    
    protected $storage;
    protected $seriesLength;
    protected $passphraseLength;
    
    public function __construct($tokens, $random, $configData)
    {
        $this->random     = $random;
        
        $storageConfig = $this->configData->slice('storage');
        $this->storage = $tokens->buildStorage($storageConfig);
        
        $this->seriesLength     = $configData->get('seriesLength',30);
        $this->passphraseLength = $configData->get('passphraseLength',30);
    }
    
    public function create($user, $lifetime)
    {
        $series     = $random->string($this->seriesLength);
        $passphrase = $random->string($this->passphraseLength);
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
        
        return $token;
    }
    
    public function refresh($token)
    {
        list($series, $passphrase) = $this->decodeToken($encodedToken);
        
        $passphrase = $random->string($this->passphraseLength);
        $challenge  = $this->challenge($series, $passphrase);
        
        $token = $this->tokenStorage->update($series, $challenge, $this->expires($lifetime));
        return $this->encodeToken($series, $passphrase);
    }
    
    public function remove($token)
    {
        list($series, $passphrase) = $this->decodeToken($encodedToken);
        $this->tokenStorage->removeSeries($series);
    }
    
    protected function storage()
    {
        if($this->storage === null) {
            
        }
        
        return $this->storage;
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