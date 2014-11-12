<?php

namespace PHPixie\Auth;

/**
 * User repository
 */
abstract class Repository
{
    /**
     * Pixie container
     * @var \PHPixie\Pixie
     */
    protected $pixie;
    
    /**
     * Auth Service
     * @var \PHPixie\Auth\Service
     */
    protected $service;
    
    /**
     * Config prefix
     * @var string
     */
    protected $config_prefix;
    
    /**
     * Builds a repository
     * @param \PHPixie\Pixie        $pixie         Pixie container
     * @param \PHPixie\Auth\Service $service       Auth service
     * @param string                $config_prefix Configuration prefix
     */
    public function __construct($pixie, $service, $config_prefix)
    {
        $this->pixie = $pixie;
        $this->service = $service;
        $this->config_prefix = $config_prefix;
    }
}