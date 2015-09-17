<?php

namespace PHPixie\Tests\Auth;

/**
 * @coversDefaultClass \PHPixie\Auth\Providers
 */
class ProvidersTest extends \PHPixie\Test\Testcase
{
    protected $handlers;
    protected $httpContextContainer;
    protected $builders = array();
    
    protected $providers;
    
    protected $types = array(
        'password'
    );
    
    public function setUp()
    {
        $this->handlers = $this->quickMock('\PHPixie\Security');
        $this->httpContextContainer = $this->quickMock('\PHPixie\HTTP\Context\Container');
        
        foreach(array('pixie', 'trixie') as $name) {
            $builder = $this->quickMock('\PHPixie\Auth\Providers\Builder');
            $this->builders[$name] = $builder;
        }
        
        $this->prepareBuilders();
        
        $this->providers = new \PHPixie\Auth\Providers(
            $this->handlers,
            $this->httpContextContainer,
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
     * @covers ::password
     * @covers ::<protected>
     */
    public function testPassword()
    {
        $handlers = $this->prepareHandlers(array('password'));
        
        $provider = $this->providerTest('password');
        $this->assertAttributes($provider, array(
            'passwordHandler' => $handlers['password']
        ));
    }
    
    /**
     * @covers ::buildFromConfig
     * @covers ::<protected>
     */
    public function testBuildFromConfig()
    {
        $this->prepareBuilders();;
        
        $this->providers = $this->getMock(
            '\PHPixie\Auth\Providers',
            $this->types,
            array(
                $this->handlers,
                $this->httpContextContainer,
                $this->builders
            )
        );
        
        $domain     = $this->getDomain();
        $configData = $this->getSliceData();
        
        foreach($this->types as $type) {
            $this->method($configData, 'getRequired', $type, array('type'), 0);
            
            $provider = $this->getProvider();
            $this->method($this->providers, $type, $provider, array($domain, 'pixie', $configData), 0);
            
            $return = $this->providers->buildFromConfig($domain, 'pixie', $configData);
            $this->assertSame($provider, $return);
        }
        
        $this->method($configData, 'getRequired', 'pixie.password', array('type'), 0);
        
        $provider = $this->getProvider();
        $builder  = $this->builders['pixie']; 
        $this->method($builder, 'buildFromConfig', $provider, array(
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
    
    protected function providerTest($type)
    {
        $domain     = $this->getDomain();
        $configData = $this->getSliceData();
        
        $providers = $this->providers;
        $provider = $providers->$type($domain, 'pixie', $configData);
        
        $class = '\PHPixie\Auth\Providers\Type\\'.ucfirst($type);
        $this->assertInstance($provider, $class, array(
            'domain'     => $domain,
            'name'       => 'pixie',
            'configData' => $configData
        ));
        
        return $provider;
    }
    
    protected function assertAttributes($instance, $propertyMap)
    {
        foreach($propertyMap as $name => $value) {
            $this->assertAttributeEquals($value, $name, $instance);
        }
    }
    
    protected function prepareHandlers($types)
    {
        $handlers = array();
        
        $at = 0;
        foreach($types as $type) {
            $handlers[$type] = $this->quickMock('\PHPixie\Security\\'.ucfirst($type));
            $this->method($this->handlers, $type, $handlers[$type], array(), $at++);
        }
        
        return $handlers;
    }
    
    protected function prepareBuilders()
    {
        foreach($this->builders as $name => $builder) {
            $this->method($builder, 'name', $name, array(), 0);
        }
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