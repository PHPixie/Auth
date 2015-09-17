<?php

namespace PHPixie\Tests;

/**
 * @coversDefaultClass \PHPixie\Auth
 */
class AuthTest extends \PHPixie\Test\Testcase
{
    protected $security;
    
    protected $auth;
    
    protected $builder;
    
    public function setUp()
    {
        $this->security   = $this->quickMock('\PHPixie\Security');
        $this->configData = $this->quickMock('\PHPixie\Slice\Data');
        $this->repositoryRegistry = $this->quickMock('\PHPixie\Auth\Repositories\Registry');
        $this->contextContainer   = $this->quickMock('\PHPixie\Auth\Context\Container');
        
        $this->auth = $this->getMockBuilder('\PHPixie\Auth')
            ->setMethods(array('buildBuilder'))
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->builder = $this->quickMock('\PHPixie\Auth\Builder');
        $this->method($this->auth, 'buildBuilder', $this->builder, array(
            $this->security
        ), 0);
        
        $this->auth->__construct(
            $this->security,
            $this->configData,
            $this->repositoryRegistry,
            $this->contextContainer
        );
    }
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstructor()
    {
        
    }
    
    /**
     * @covers ::buildBuilder
     * @covers ::<protected>
     */
    public function testBuildBuilder()
    {
        $this->auth = new \PHPixie\Auth(
            $this->security,
            $this->configData,
            $this->repositoryRegistry,
            $this->contextContainer
        );
        
        $builder = $this->auth->builder();
        $this->assertInstance($builder, '\PHPixie\Auth\Builder', array(
            'security'           => $this->security,
            'configData'         => $this->configData,
            'repositoryRegistry' => $this->repositoryRegistry,
            'contextContainer'   => $this->contextContainer
        ));
    }
    
    /**
     * @covers ::builder
     * @covers ::<protected>
     */
    public function testBuilder()
    {
        $this->assertSame($this->builder, $this->auth->builder());
    }
    
    /**
     * @covers ::domains
     * @covers ::<protected>
     */
    public function testDomains()
    {
        $domains = $this->quickMock('\PHPixie\Auth\Domains');
        $this->method($this->builder, 'domains', $domains, array(), 0);
        $this->assertSame($domains, $this->auth->domains());
    }
    
    /**
     * @covers ::domain
     * @covers ::<protected>
     */
    public function testDomain()
    {
        $domains = $this->quickMock('\PHPixie\Auth\Domains');
        $this->method($this->builder, 'domains', $domains, array());
        
        $domain  = $this->quickMock('\PHPixie\Auth\Domain');
        $this->method($domains, 'get', $domain, array('pixie'), 0);
        
        $this->assertSame($domain, $this->auth->domain('pixie'));
        
        $domain  = $this->quickMock('\PHPixie\Auth\Domain');
        $this->method($domains, 'get', $domain, array('default'), 0);
        
        $this->assertSame($domain, $this->auth->domain());
    }
    
    /**
     * @covers ::context
     * @covers ::<protected>
     */
    public function testContext()
    {
        $context = $this->quickMock('\PHPixie\Auth\Context');
        $this->method($this->builder, 'context', $context, array(), 0);
        
        $this->assertSame($context, $this->auth->context());
    }
}