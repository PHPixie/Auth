<?php

namespace PHPixie\Auth;

class Domains implements \PHPixie\Bundles\Registry
{
    protected $builder;
    protected $configData;
    
    protected $domains;
    
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
            $domains[$name] = $this->builder->buildDomain($domainConfig);
        }
        
        $this->domains = $domains;
    }
}