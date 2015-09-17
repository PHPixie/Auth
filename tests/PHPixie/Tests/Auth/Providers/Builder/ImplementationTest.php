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