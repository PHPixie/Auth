<?php

namespace PHPixie\Tests\Auth\Providers\Provider;

/**
 * @coversDefaultClass \PHPixie\Auth\Providers\Provider\Implementation
 */
abstract class ImplementationTest extends \PHPixie\Test\Testcase
{
    protected $domain;
    protected $name;
    protected $configData;
    
    protected $provider;
    
    public function setUp()
    {
        $this->domain     = $this->quickMock('\PHPixie\Auth\Domains\Domain');
        $this->configData = $this->getSliceData();
        
        $this->provider = $this->provider();
    }
    
    /**
     * @covers \PHPixie\Auth\Providers\Provider\Implementation::__construct
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
        $this->assertSame($this->name, $this->provider->name());
    }
    
    /**
     * @covers ::name
     * @covers ::<protected>
     */
    public function name()
    {
        $this->assertSame($this->name, $this->provider->name());
    }
    
    protected function prepareRepository(&$domainAt = 0)
    {
        $repository = $this->getRepository();
        $this->method($this->domain, 'repository', $repository, array(), $domainAt++);
        return $repository;
    }
    
    protected function getRepository()
    {
        return $this->quickMock('\PHPixie\Auth\Repositories\Repository');
    }
    
    protected function getUser()
    {
        return $this->quickMock('\PHPixie\Auth\Repositories\Repository\User');
    }
    
    protected function getSliceData()
    {
        return $this->quickMock('\PHPixie\Slice\Data');
    }
    
    abstract protected function provider();
}