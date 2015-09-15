<?php

namespace PHPixie\Tests\Auth;

/**
 * @coversDefaultClass \PHPixie\Auth\Domains
 */
class DomainsTest extends \PHPixie\Test\Testcase
{
    protected $builder;
    protected $configData;
    
    protected $domains;
    
    protected $instances = array();
    
    public function setUp()
    {
        $this->builder    = $this->quickMock('\PHPixie\Auth\Builder');
        $this->configData = $this->getSliceData();
        
        $this->domains = new \PHPixie\Auth\Domains(
            $this->builder,
            $this->configData
        );
        
        foreach(array('pixie', 'trixie') as $name) {
            $this->instances[$name] = $this->quickMock('\PHPixie\Auth\Domains\Domain');
        }
    }
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstruct()
    {
    
    }
    
    /**
     * @covers ::asArray
     * @covers ::<protected>
     */
    public function testAsArray()
    {
        $this->prepareRequireDomains();
        for($i=0; $i<2; $i++) {
            $this->assertSame($this->instances, $this->domains->asArray());
        }
    }
    
    /**
     * @covers ::get
     * @covers ::<protected>
     */
    public function testGet()
    {
        $this->prepareRequireDomains();
        foreach($this->instances as $name => $domain) {
            for($i=0; $i<2; $i++) {
                $this->assertSame($domain, $this->domains->get($name));
            }
        }
        
        $domains = $this->domains;
        $this->assertException(function() use($domains) {
            $domains->get('nope');
        }, '\PHPixie\Auth\Exception');
    }
    
    /**
     * @covers ::checkUser
     * @covers ::<protected>
     */
    public function testCheckUser()
    {
        $this->prepareRequireDomains();
        foreach($this->instances as $name => $domain) {
            for($i=0; $i<2; $i++) {
                $this->method($domain, 'checkUser', null, array(), 0);
            }
        }
        
        $this->domains->checkUser();
    }
    
    protected function prepareRequireDomains()
    {
        $domains = $this->instances;
        
        $configAt  = 0;
        $this->method($this->configData, 'keys', array_keys($domains), array(), $configAt++);
        
        $builderAt = 0;
        foreach($domains as $name => $domain) {
            $domainConfig = $this->getSliceData();
            $this->method($this->configData, 'slice', $domainConfig, array($name), $configAt++);
            
            $this->method($this->builder, 'buildDomain', $domain, array(
                $name,
                $domainConfig
            ), $builderAt++);
        }
    }
    
    protected function getSliceData()
    {
        return $this->quickMock('\PHPixie\Slice\Data');
    }
}