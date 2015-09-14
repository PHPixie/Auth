<?php

namespace PHPixie\Tests\Auth\Handlers;

/**
 * @coversDefaultClass \PHPixie\Auth\Handlers\Password
 */
class PasswordTest extends \PHPixie\Test\Testcase
{
    protected $password;
    
    public function setUp()
    {
        $this->password = new \PHPixie\Auth\Handlers\Password();
    }
    
    /**
     * @covers ::hash
     * @covers ::verify
     * @covers ::<protected>
     */
    public function testPassword()
    {
        $hash = $this->password->hash('test');
        
        $this->assertTrue($this->password->verify('test', $hash));
        $this->assertFalse($this->password->verify('blum', $hash));
    }
}