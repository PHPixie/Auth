<?php

namespace PHPixie\Tests\Auth;

/**
 * @coversDefaultClass \PHPixie\Auth\Handlers
 */
class HandlersTest extends \PHPixie\Test\Testcase
{
    protected $database;
    
    protected $handlers;
    
    public function setUp()
    {
        $this->database = $this->quickMock('\PHPixie\Database');
        
        $this->handlers = new \PHPixie\Auth\Handlers($this->database);
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
        $this->instanceTest('password', '\PHPixie\Auth\Handlers\Password');
    }
    
    /**
     * @covers ::random
     * @covers ::<protected>
     */
    public function testRandom()
    {
        $this->instanceTest('random', '\PHPixie\Auth\Handlers\Random');
    }
    
    /**
     * @covers ::tokens
     * @covers ::<protected>
     */
    public function testTokens()
    {
        $this->instanceTest('tokens', '\PHPixie\Auth\Handlers\Tokens', array(
            'handlers' => $this->handlers,
            'database' => $this->database
        ));
    }
    
    protected function instanceTest($method, $class, $attributeMap = array())
    {
        $instance = $this->handlers->$method();
        
        $this->assertInstance($instance, $class, $attributeMap);
        $this->assertSame($instance, $this->handlers->$method());
    }
}