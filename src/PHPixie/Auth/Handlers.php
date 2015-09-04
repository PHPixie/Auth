<?php

namespace PHPixie\Auth;

class Handlers
{
    protected $database;
    
    protected $instances = array();
    
    public function __construct($database)
    {
        $this->database = $database;
    }
    
    public function password()
    {
        return $this->instance('password');
    }
    
    public function random()
    {
        return $this->instance('random');
    }
    
    public function tokens()
    {
        return $this->instance('tokens');
    }
    
    protected function instance($name)
    {
        if(!array_key_exists($name, $this->instances)) {
            $method = 'build'.ucfirst($name);
            $this->instances[$name] = $this->$method();
        }
        
        return $this->instances[$name];
    }
    
    protected function buildPassword()
    {
        return new Handlers\Password();
    }
    
    protected function buildRandom()
    {
        return new Handlers\Random();
    }
    
    protected function buildTokens()
    {
        return new Handlers\Tokens(
            $this,
            $this->database
        );
    }
}