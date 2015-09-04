<?php

namespace PHPixie\Auth\Handlers\Tokens;

class Token
{
    protected $userId;
    protected $series;
    protected $challenge;
    protected $expires;
    protected $string;
    
    public function __construct($series, $userId, $challenge, $expires, $string = null)
    {
        $this->userId     = $userId;
        $this->series     = $series;
        $this->challenge  = $challenge;
        $this->expires  = $expires;
        $this->string  = $string;
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
    
    public function expires()
    {
        return $this->expires;
    }
    
    public function string()
    {
        return $this->string;
    }
}