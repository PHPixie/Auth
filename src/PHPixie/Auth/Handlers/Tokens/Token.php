<?php

namespace PHPixie\Auth\Persistance;

class Token
{
    protected $userId;
    protected $series;
    protected $challenge;
    
    public function __construct($userId, $series, $challenge)
    {
        $this->userId     = $userId;
        $this->series     = $series;
        $this->challenge  = $challenge;
    }
    
    public function userId()
    {
        return $this->userId;
    }
    
    public function series()
    {
        return $this->series;
    }
    
    public function challenge()
    {
        return $this->challenge;
    }
}