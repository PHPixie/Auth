<?php

namespace PHPixie\Tests\Auth;

/**
 * @coversDefaultClass \PHPixie\Auth\Builder
 */
class BuilderTest extends \PHPixie\Test\Testcase
{
    protected $configData;
    protected $repositoryRegistry;
    protected $providerBuilders;
    protected $contextContainer;
    
    protected $builder;
    
    public function setUp()
    {
        $this->configData = $this->getSliceData();
        $this->repositoryRegistry = $this->quickMock('\PHPixie\Auth\Repositories\Registry');
        $this->providerBuilders   = array($this->quickMock('\PHPixie\Auth\Providers\Provider'));
        $this->contextContainer   = $this->getContextContainer();
        
        $this->builder = new \PHPixie\Auth\Builder(
            $this->configData,
            $this->repositoryRegistry,
            $this->providerBuilders,
            $this->contextContainer
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
     * @covers ::domains
     * @covers ::<protected>
     */
    public function testDomains()
    {
        $domainConfig = $this->getSliceData();
        $this->method($this->configData, 'slice', $domainConfig, array('domains'), 0);
        
        $this->instanceTest('domains', '\PHPixie\Auth\Domains', array(
            'builder'    => $this->builder,
            'configData' => $domainConfig
        ));
    }
    
    /**
     * @covers ::providers
     * @covers ::<protected>
     */
    public function testProviders()
    {
        $builder = $this->providerBuilders[0];
        $this->method($builder, 'name', 'trixie', array(), 0);
        
        $this->instanceTest('providers', '\PHPixie\Auth\Providers', array(
            'builders' => array('trixie' => $builder)
        ));
    }
    
    /**
     * @covers ::repositories
     * @covers ::<protected>
     */
    public function testRepositories()
    {
        $this->instanceTest('repositories', '\PHPixie\Auth\Repositories', array(
            'repositoryRegistry' => $this->repositoryRegistry
        ));
    }
    
    /**
     * @covers ::buildDomain
     * @covers ::<protected>
     */
    public function testBuildDomain()
    {
        $configData = $this->getSliceData();
        
        $domain = $this->builder->buildDomain('pixie', $configData);
        
        $this->assertInstance($domain, '\PHPixie\Auth\Domains\Domain', array(
            'builder'    => $this->builder,
            'configData' => $configData
        ));
    }
    
    /**
     * @covers ::buildContext
     * @covers ::<protected>
     */
    public function testBuildContext()
    {
        $context = $this->builder->buildContext();
        
        $this->assertInstance($context, '\PHPixie\Auth\Context');
    }
    
    /**
     * @covers ::buildContextContainer
     * @covers ::<protected>
     */
    public function testBuildContextContainer()
    {
        $context   = $this->getContext();
        
        $container = $this->builder->buildContextContainer($context);
        $this->assertInstance($container, '\PHPixie\Auth\Context\Container\Implementation', array(
            'context' => $context
        ));
        
        $container = $this->builder->buildContextContainer();
        $this->assertInstance($container, '\PHPixie\Auth\Context\Container\Implementation', array(
            'context' => null
        ));
    }
    
    /**
     * @covers ::contextContainer
     * @covers ::<protected>
     */
    public function testContextContainer()
    {
        $this->assertSame($this->contextContainer, $this->builder->contextContainer());
        
        $this->builder = $this->getMock(
            '\PHPixie\Auth\Builder',
            array('buildContextContainer'),
            array(
                $this->configData,
                $this->repositoryRegistry,
                $this->providerBuilders
            )
        );
        
        $container = $this->getContextContainer();
        $this->method($this->builder, 'buildContextContainer', $container, array(), 0);
        for($i=0; $i<2; $i++) {
            $this->assertSame($container, $this->builder->contextContainer());
        }
    }
    
    /**
     * @covers ::context
     * @covers ::<protected>
     */
    public function testContext()
    {
        $context = $this->getContext();
        $this->method($this->contextContainer, 'authContext', $context, array(), 0);
        
        $this->assertSame($context, $this->builder->context());
    }
    
    protected function instanceTest($method, $class, $propertyMap = array())
    {
        $instance = $this->builder->$method();
        $this->assertInstance($instance, $class, $propertyMap);
        $this->assertSame($instance, $this->builder->$method());
    }
    
    protected function getContext()
    {
        return $this->quickMock('\PHPixie\Auth\Context');
    }
    
    protected function getContextContainer()
    {
        return $this->quickMock('\PHPixie\Auth\Context\Container');
    }
    
    protected function getSliceData()
    {
        return $this->quickMock('\PHPixie\Slice\Data');
    }
}