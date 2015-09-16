<?php

namespace PHPixie\Auth;

class Repositories
{
    protected $repositoryRegistry;
    
    public function __construct($repositoryRegistry = null)
    {
        $this->repositoryRegistry = $repositoryRegistry;
    }
    
    public function get($name)
    {
        if($this->repositoryRegistry !== null) {
            $repository = $this->repositoryRegistry->repository($name);
            if($repository !== null) {
                return $repository;
            }
        }
        
        throw new \PHPixie\Auth\Exception("Repository '$name' does not exist");
    }
}