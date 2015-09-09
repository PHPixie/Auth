<?php

namespace PHPixie\Tests\Auth\Handlers;

/**
 * @coversDefaultClass \PHPixie\Auth\Handlers\Random
 */
class RandomTest extends \PHPixie\Test\Testcase
{
    protected $random;
    
    public function setUp()
    {
        $this->random = new \PHPixie\Auth\Handlers\Random();
    }
    
    /**
     * @covers ::bytes
     * @covers ::<protected>
     */
    public function testBytes()
    {
        $bytes = $this->random->bytes(10);
        $this->assertSame(10, strlen($bytes));
    }
    
    /**
     * @covers ::string
     * @covers ::<protected>
     */
    public function testString()
    {
        foreach(array(5, 10) as $length) {
            $string = $this->random->string($length);
            $this->assertSame(true, ctype_alnum($string));
            $this->assertSame($length, strlen($string));
        }
    }
}