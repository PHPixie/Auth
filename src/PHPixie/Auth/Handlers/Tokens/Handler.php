<?php

namespace PHPixie\Auth\Handlers\Tokens;

class Handler
{
    protected $tokens;
    protected $random;
    protected $configData;
    
    protected $storage;
    protected $seriesLength;
    protected $passphraseLength;
    
    public function __construct($tokens, $random, $configData)
    {
        $this->tokens     = $tokens;
        $this->random     = $random;
        
        $storageConfig = $configData->slice('storage');
        $this->storage = $tokens->buildStorage($storageConfig);
        
        $this->seriesLength     = $configData->get('seriesLength',30);
        $this->passphraseLength = $configData->get('passphraseLength',30);
    }
    
    public function create($user, $lifetime)
    {
        $series     = $this->random->string($this->seriesLength);
        $passphrase = $this->random->string($this->passphraseLength);
        $challenge  = $this->challenge($series, $passphrase);
        
        $token = $this->tokens->token(
            $series,
            $user->id(),
            $challenge,
            $this->expires($lifetime),
            $this->encodeToken($series, $passphrase)
        );
        
        $this->storage->insert($token);
        return $token;
    }
    
    public function getByString($encodedToken)
    {
        $token = $this->decodeToken($encodedToken);
        
        if($token === null) {
            return null;
        }
        
        list($series, $passphrase) = $token;
        
        $token = $this->storage->get($series);
        
        if($token === null) {
            return null;
        }
        
        if($this->challenge($series, $passphrase) !== $token->challenge()) {
            $this->storage->remove($series);
            return null;
        }
        
        return $token;
    }
    
    public function removeByString($encodedToken)
    {
        $token = $this->decodeToken($encodedToken);
        
        if($token === null) {
            return;
        }
        
        list($series, $passphrase) = $token;
        $this->storage->remove($series);
    }
    
    public function refresh($token)
    {
        $passphrase = $this->random->string($this->passphraseLength);
        $challenge  = $this->challenge($token->series(), $passphrase);
        
        $token = $this->tokens->token(
            $token->series(),
            $token->userId(),
            $challenge,
            $token->expires(),
            $this->encodeToken($token->series(), $passphrase)
        );
        
        $this->storage->update($token);
        return $token;
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
    
    protected function expires($lifetime)
    {
        return time()+$lifetime;
    }
}