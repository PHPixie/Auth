<?php

namespace PHPixie\Auth;

class Token
{
    protected $userId;
    protected $series;
    protected $passphrase;
    
    public function __construct($userId, $series, $passphrase)
    {
        $this->userId     = $userId;
        $this->series     = $series;
        $this->passphrase = $passphrase;
    }
    
    public function userId()
    {
        return $this->userId;
    }
    
    public function series()
    {
        return $this->series;
    }
    
    public function passphrase()
    {
        return $this->passphrase;
    }
    
    public function setPassphrase($passphrase, $challenge);
}