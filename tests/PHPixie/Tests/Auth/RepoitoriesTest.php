<?php

namespace PHPixie\Tests\Auth;

/**
 * @coversDefaultClass \PHPixie\Auth\Repositories
 */
class RepositoriesTest extends \PHPixie\Test\Testcase
{
    protected $repositoryRegistry;
    
    protected $repositories;
    
    public function setUp()
    {
        $this->repositoryRegistry = $this->quickMock('\PHPixie\Auth\Repositories\Registry');
        
        $this->repositories = new \PHPixie\Auth\Repositories($this->repositoryRegistry);
    }
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstruct()
    {
    
    }
    
    /**
     * @covers ::get
     * @covers ::<protected>
     */
    public function testGet()
    {
        $repository = $this->quickMock('\PHPixie\Auth\Repositories\Repository');
        $this->method($this->repositoryRegistry, 'repository', $repository, array('pixie'), 0);
        
        $this->assertSame($repository, $this->repositories->get('pixie'));
        
        $this->method($this->repositoryRegistry, 'repository', null, array('pixie'), 0);
        $this->assertNoRepository('pixie');
        
        $this->repositories = new \PHPixie\Auth\Repositories();
        $this->assertNoRepository('pixie');
    }
    
    protected function assertNoRepository($name)
    {
        $repositories = $this->repositories;
        $this->assertException(function () use($repositories, $name) {
            $repositories->get($name);
        }, '\PHPixie\Auth\Exception');
    }
}