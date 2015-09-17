<?php

namespace PHPixie\Tests\Auth;

/**
 * @coversDefaultClass \PHPixie\Auth\Providers
 */
class ProvidersTest extends \PHPixie\Test\Testcase
{
    protected $builders = array();
    
    protected $providers;
    
    public function setUp()
    {
        foreach(array('pixie', 'trixie') as $name) {
            $builder = $this->quickMock('\PHPixie\Auth\Providers\Builder');
            $this->builders[$name] = $builder;
            $this->method($builder, 'name', $name, array(), 0);
        }
        
        $this->providers = new \PHPixie\Auth\Providers(
            $this->builders
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
     * @covers ::buildFromConfig
     * @covers ::<protected>
     */
    public function testBuildFromConfig()
    {
        $domain     = $this->getDomain();
        $configData = $this->getSliceData();
        $this->method($configData, 'getRequired', 'pixie.password', array('type'), 0);
        
        $provider = $this->getProvider();
        $builder  = $this->builders['pixie']; 
        $this->method($builder, 'buildProvider', $provider, array(
            'password',
            $domain,
            'pixie',
            $configData
        ), 0);
        
        $return = $this->providers->buildFromConfig($domain, 'pixie', $configData);
        $this->assertSame($provider, $return);
        
        $this->assertNoProvider('none');
    }
    
    protected function assertNoProvider($type)
    {
        $domain     = $this->getDomain();
        $configData = $this->getSliceData();
        
        $this->method($configData, 'getRequired', $type, array('type'), 0);
        
        $providers = $this->providers;
        $this->assertException(function() use($providers, $type, $domain, $configData) {
            $providers->buildFromConfig($domain, 'pixie', $configData);
        }, '\PHPixie\Auth\Exception');
    }
    
    protected function getProvider()
    {
        return $this->quickMock('\PHPixie\Auth\Providers\Provider');
    }
    
    protected function getDomain()
    {
        return $this->quickMock('\PHPixie\Auth\Domains\Domain');
    }
    
    protected function getSliceData()
    {
        return $this->quickMock('\PHPixie\Slice\Data');
    }
}