<?php

namespace PHPixie\Tests\Auth\Domains

/**
 * @coversDefaultClass \PHPixie\Auth\Domains\Domain
 */
class DomainTest extends \PHPixie\Test\Testcase
{
    protected $builder;
    protected $name = 'pixie';
    protected $configData;
    
    protected $domain;
    
    protected $providers = array();
    
    public function setUp()
    {
        $this->builder    = $this->quickMock('\PHPixie\Auth\Builder');
        $this->configData = $this->getSliceData();
        
        $this->domain = new \PHPixie\Auth\Domains\Domain(
            $this->builder,
            $this->name,
            $this->configData
        );
    }
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstruct()
    {
        
    }
    
    /**
     * @covers ::name
     * @covers ::<protected>
     */
    public function testName()
    {
        $this->assertSame($this->name, $this->domain->name());
    }
    
    /**
     * @covers ::providers
     * @covers ::<protected>
     */
    public function testProviders()
    {
        $providers = array(
            'pixie'  => $this->getProvider(),
            'trixie' => $this->getProvider(),
        );
        
        
        for($i=0; $i<2; $i++) {
            $this->assertSame($providers, $this->domain->providers());
        }
    }
    
    /**
     * @covers ::provider
     * @covers ::<protected>
     */
    public function testProvider()
    {
        $providers = array(
            'pixie'  => $this->getProvider(),
            'trixie' => $this->getProvider(),
        );
        
        foreach($providers as $name => $provider) {
            $this->assertSame($provider, $this->domain->provider($name));
        }
    }
    
    protected function prepareRequireProviders($providers)
    {
        $builderAt = 0;
        
        $providerBuilder = $this->quickMock('\PHPixie\Auth\Providers');
        $this->method($this->builder, 'providers', $providerBuilder, array(), $builderAt++);
        
        $providersConfig = $this->getSliceData();
        $this->method($this->configData, 'slice', $providersConfig, array('providers'), 0);
        
        $configAt  = 0;
        $this->method($providersConfig, 'keys', array_keys($providers), array(), $configAt++);
        
        foreach($providers as $name => $provider) {
            $providerConfig = $this->getSliceData();
            $this->method($providersConfig, 'slice', $provider, array($name), $configAt++);
            
            $this->method($this->builder, 'buildFromConfig', $provider, array(
                $this->domain,
                $name,
                $providerConfig
            ), $configAt++);
        }
    }
    
    protected function getSliceData()
    {
        return $this->quickMock('\PHPixie\Slice\Data');
    }
    
    protected function getProvider()
    {
        return $this->quickMock('\PHPixie\Auth\Providers\Provider');
    }
}