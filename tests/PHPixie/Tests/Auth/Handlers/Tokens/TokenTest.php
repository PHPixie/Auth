<?php

namespace PHPixie\Tests\Auth\Handlers\Tokens;

/**
 * @coversDefaultClass \PHPixie\Auth\Handlers\Tokens\Token
 */
class TokenTest extends \PHPixie\Test\Testcase
{
    protected $series    = 'pixie';
    protected $userId    = 5;
    protected $challenge = 'trixie';
    protected $expires   = '123';
    protected $string    = 'pixie:trixie';
    
    protected $token;
    
    public function setUp()
    {
        $this->token = new \PHPixie\Auth\Handlers\Tokens\Token(
            $this->series,
            $this->userId,
            $this->challenge,
            $this->expires,
            $this->string
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
     * @covers ::series
     * @covers ::userId
     * @covers ::challenge
     * @covers ::expires
     * @covers ::string
     * @covers ::<protected>
     */
    public function testValues()
    {
        $methods = array(
            'series',
            'userId',
            'challenge',
            'expires',
            'string'
        );
        
        foreach($methods as $name) {
            $result = call_user_func(array($this->token, $name));
            $this->assertSame($this->$name, $result);
        }
    }
    
    /**
     * @covers ::string
     * @covers ::<protected>
     */
    public function testNoString()
    {
        $this->token = new \PHPixie\Auth\Handlers\Tokens\Token(
            $this->userId,
            $this->series,
            $this->challenge,
            $this->expires
        );
        
        $this->assertSame(null, $this->token->string());
    }
}