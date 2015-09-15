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
    
    public function asArray()
    {
        $this->requireDomains();
        return $this->domains;
    }
    
    public function get($name)
    {
        $this->requireDomains();
        if(array_key_exists($name, $this->domains)) {
            return $this->domains[$name];
        }
        
        throw new \PHPixie\Auth\Exception("Domain '$name' does not exist");
    }    
    
    public function checkUser()
    {
        $this->requireDomains();
        foreach($this->domains as $domain) {
            $domain->checkUser();
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
            $domains[$name] = $this->builder->buildDomain($name, $domainConfig);
        }
        
        $this->domains = $domains;
    }
}