<?php

namespace PHPixie\Auth;

class Domains
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @var \PHPixie\Slice\Type\ArrayData
     */
    protected $configData;

    /**
     * @var Domains\Domain[]
     */
    protected $domains = null;

    /**
     * @param Builder $builder
     * @param \PHPixie\Slice\Type\ArrayData $configData
     */
    public function __construct($builder, $configData)
    {
        $this->builder    = $builder;
        $this->configData = $configData;
    }

    /**
     * @return Domains\Domain[]
     */
    public function asArray()
    {
        $this->requireDomains();
        return $this->domains;
    }

    /**
     * @param string $name
     * @return Domains\Domain
     * @throws Exception
     */
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