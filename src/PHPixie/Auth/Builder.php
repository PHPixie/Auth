<?php

namespace PHPixie\Auth;

class Builder
{
    /**
     * @var \PHPixie\Slice\Type\ArrayData
     */
    protected $configData;

    /**
     * @var \PHPixie\Bundles\Auth
     */
    protected $repositoryRegistry;

    /**
     * @var Providers\Builder[]
     */
    protected $providerBuilders;

    /**
     * @var \PHPixie\Framework\Context
     */
    protected $contextContainer;
    
    protected $instances = array();

    /**
     * @param \PHPixie\Slice\Type\ArrayData $configData
     * @param \PHPixie\Bundles\Auth         $repositoryRegistry
     * @param Providers\Builder[]           $providerBuilders
     * @param \PHPixie\Framework\Context    $contextContainer
     */
    public function __construct(
        $configData,
        $repositoryRegistry = null,
        $providerBuilders   = array(),
        $contextContainer   = null
    )
    {
        $this->configData         = $configData;
        $this->repositoryRegistry = $repositoryRegistry;
        $this->providerBuilders   = $providerBuilders;
        $this->contextContainer   = $contextContainer;
    }

    /**
     * @return Domains
     */
    public function domains()
    {
        return $this->instance('domains');
    }

    /**
     * @return Providers
     */
    public function providers()
    {
        return $this->instance('providers');
    }

    /**
     * @return Repositories
     */
    public function repositories()
    {
        return $this->instance('repositories');
    }

    /**
     * @return Context\Container\Implementation
     */
    public function contextContainer()
    {
        if($this->contextContainer === null) {
            $context = $this->buildContext();
            $this->contextContainer = $this->buildContextContainer($context);
        }
        
        return $this->contextContainer;
    }

    /**
     * @return Context
     */
    public function context()
    {
        return $this->contextContainer()->authContext();
    }

    /**
     * @return Context
     */
    public function buildContext()
    {
        return new Context();
    }

    /**
     * @param Context $context
     * @return Context\Container\Implementation
     */
    public function buildContextContainer($context = null)
    {
        return new Context\Container\Implementation($context);
    }

    /**
     * @param string $name
     * @param \PHPixie\Slice\Type\ArrayData $configData
     * @return Domains\Domain
     */
    public function buildDomain($name, $configData)
    {
        return new Domains\Domain($this, $name, $configData);
    }
    
    protected function instance($name)
    {
        if(!array_key_exists($name, $this->instances)) {
            $method = 'build'.ucfirst($name);
            $this->instances[$name] = $this->$method();
        }
        
        return $this->instances[$name];
    }
    
    protected function buildDomains()
    {
        return new Domains(
            $this,
            $this->configData->slice('domains')
        );
    }
    
    protected function buildProviders()
    {
        return new Providers(
            $this->providerBuilders
        );
    }
    
    protected function buildRepositories()
    {
        return new Repositories(
            $this->repositoryRegistry
        );
    }
}
