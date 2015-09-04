<?php

namespace PHPixie\Auth\Handlers;

class Random
{
    public function string($length)
    {
        $bytesLength = (int) ceil($length/2);
        $bytes = $this->bytes($bytesLength);
        $string = bin2hex($bytes);
        return substr($string, 0, $length);
        
    }
    
    public function bytes($length)
    {
        return random_bytes($length);
    }
}