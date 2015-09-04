<?php

namespace PHPixie\Auth;

class Domains
{
    protected $builder;
    protected $configData;
    
    protected $domains = null;
    
    public function __construct($builder, $configData)
    {
        $this->builder    = $builder;
        $this->configData = $configData;
    }
    
    public function domains()
    {
        $this->requireDomains();
        return $this->domains;
    }
    
    public function get($name)
    {
        $this->requireDomains();
        return $this->domains[$name];
    }    
    
    public function check()
    {
        $this->requireDomains();
        foreach($this->domains as $domain) {
            $domain->check();
        }
    }
    
    protected function requireDomains()
    {
        if($this->domains !== null) {
            return;
        }
        
        $domains = array();
        foreach($this->configData->keys() as $name) {
            $domainConfig = $this->configData->slice($name);
            $domains[$name] = $this->buildDomain($name, $domainConfig);
        }
        
        $this->domains = $domains;
    }
    
    protected function buildDomain($name, $configData)
    {
        return new Domains\Domain($this->builder, $name, $configData);
    }
}