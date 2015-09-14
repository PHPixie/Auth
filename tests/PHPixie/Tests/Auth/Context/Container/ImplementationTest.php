<?php

namespace PHPixie\Tests\Auth\Context\Container;

/**
 * @coversDefaultClass PHPixie\Auth\Context\Container\Implementation
 */
class ImplementationTest extends \PHPixie\Test\Testcase
{
    protected $context;
    protected $container;
    
    public function setUp()
    {
        $this->context = $this->quickMock('\PHPixie\Tests\Auth\Context');
        $this->container = new \PHPixie\Auth\Context\Container\Implementation(
            $this->context
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
     * @covers ::authContext
     * @covers ::<protected>
     */
    public function testAuthContext()
    {
        $this->assertSame($this->context, $this->container->authContext());
    }
}