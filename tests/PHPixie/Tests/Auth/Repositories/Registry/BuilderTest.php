<?php

namespace PHPixie\Tests\Auth\Repositories\Registry;

/**
 * @coversDefaultClass \PHPixie\Auth\Repositories\Registry\Builder
 */
class BuilderTest extends \PHPixie\Test\Testcase
{
    /**
     * @covers ::repository
     * @covers ::<protected>
     */
    public function testRepository()
    {
        $builderMock = $this->registryMock(array(
            'buildPixieRepository'
        ));
        
        $this->assertSame(null, $builderMock->repository('trixie'));
        
        $repository = $this->getRepository();
        $this->method($builderMock, 'buildPixieRepository', $repository, array(), 0);
        
        for($i=0; $i<2; $i++) {
            $this->assertSame($repository, $builderMock->repository('pixie'));
        }
    }
    
    protected function getRepository()
    {
        return $this->quickMock('\PHPixie\Auth\Repositories\Repository');
    }
    
    protected function registryMock($methods = array())
    {
        return $this->quickMock(
            '\PHPixie\Auth\Repositories\Registry\Builder',
            $methods
        );
    }
}