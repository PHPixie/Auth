<?php

namespace PHPixie\Auth\Repositories\Registry;

abstract class Builder implements \PHPixie\Auth\Repositories\Registry
{
    protected $repositories = array();
    
    public function repository($name)
    {
        if(!array_key_exists($name, $this->repositories)) {
            $method = 'build'.ucfirst($name).'Repository';
            if(!method_exists($this, $method)) {
                return null;
            }
            $this->repositories[$name] = $this->$method();
        }
        
        return $this->repositories[$name];
    }
}