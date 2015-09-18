<?php

namespace PHPixie\Tests\Auth\Providers\Builder;

/**
 * @coversDefaultClass \PHPixie\Auth\Providers\Builder\Implementation
 */
class BuilderTest extends \PHPixie\Test\Testcase
{
    /**
     * @covers ::buildPovider
     * @covers ::<protected>
     */
    public function testBuildProvider()
    {
        $builderMock = $this->builderMock(array(
            'buildPixieProvider'
        ));
        
        $domain     = $this->getDomain();
        $configData = $this->getSliceData();
        
        $provider = $this->quickMock('\PHPixie\Auth\Providers\Provider');
        $this->method($builderMock, 'buildPixieProvider', $provider, array(
            $domain,
            'trixie',
            $configData
        ), 0);
        
        $this->assertSame($provider, $builderMock->buildProvider(
            'pixie',
            $domain,
            'trixie',
            $configData
        ));
    }
    
    protected function providerTest($type)
    {
        $domain     = $this->getDomain();
        $configData = $this->getSliceData();
        
        $method = 'build'.ucfirst($type).'Provider';
        $providers = $this->providers;
        $provider = $providers->$method($domain, 'pixie', $configData);
        
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
    
    protected function getDomain()
    {
        return $this->quickMock('\PHPixie\Auth\Domains\Domain');
    }
    
    protected function getSliceData()
    {
        return $this->quickMock('\PHPixie\Slice\Data');
    }
    
    protected function builderMock($methods = array())
    {
        return $this->abstractMock(
            '\PHPixie\Auth\Providers\Builder\Implementation',
            $methods
        );
    }
}