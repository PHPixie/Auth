<?php

namespace PHPixie\Auth;

class Repositories
{
    /**
     * @var \PHPixie\Bundles\Auth
     */
    protected $repositoryRegistry;

    /**
     * @param \PHPixie\Bundles\Auth $repositoryRegistry
     */
    public function __construct($repositoryRegistry = null)
    {
        $this->repositoryRegistry = $repositoryRegistry;
    }

    /**
     * @param string $name
     * @return \PHPixie\AuthLogin\Repository
     * @throws Exception
     * @throws \PHPixie\Bundles\Exception
     */
    public function get($name)
    {
        if($this->repositoryRegistry !== null) {
            $repository = $this->repositoryRegistry->repository($name);
            if($repository !== null) {
                return $repository;
            }
        }
        
        throw new Exception("Repository '$name' does not exist");
    }
}