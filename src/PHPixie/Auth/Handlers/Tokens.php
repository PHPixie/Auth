<?php

namespace PHPixie\Auth\Handlers;

class Tokens
{
    public function token($userId, $series, $challenge)
    {
        return new Tokens\Token($userId, $series, $challenge);
    }
    
    
}