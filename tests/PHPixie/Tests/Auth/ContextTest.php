<?php

namespace PHPixie\Tests\Auth;

/**
 * @coversDefaultClass \PHPixie\Auth\Context
 */
class ContextTest extends \PHPixie\Test\Testcase
{
    protected $context;
    
    public function setUp()
    {
        $this->context = new \PHPixie\Auth\Context();
    }
    
    /**
     * @covers ::setUser
     * @covers ::user
     * @covers ::unsetUser
     * @covers ::usedProvider
     * @covers ::<protected>
     */
    public function testContext()
    {
        $this->assertNoUser();
        
        $defaultUser = $this->getUser();
        $pixieUser = $this->getUser();
        
        $this->context->setUser($defaultUser);
        $this->context->setUser($pixieUser, 'pixie', 'password');
        
        $this->assertSame($defaultUser, $this->context->user());
        $this->assertSame(null, $this->context->usedProvider());
        
        $this->assertSame($pixieUser, $this->context->user('pixie'));
        $this->assertSame('password', $this->context->usedProvider('pixie'));
        
        $this->context->unsetUser();
        $this->assertNoUser();
        
        $this->context->unsetUser('pixie');
        $this->assertNoUser('pixie');
        
        $this->assertNoUser('trixie');
        $this->context->unsetUser('trixie');
    }
    
    protected function assertNoUser($domain = null)
    {
        $params = array();
        if($domain !== null) {
            $params[]= $domain;
        }
        
        foreach(array('user', 'usedProvider') as $method) {
            $callback = array($this->context, $method);
            $result = call_user_func_array($callback, $params);
            $this->assertSame(null, $result);
        }
    }
    
    protected function getUser()
    {
        return $this->quickMock('\PHPixie\Auth\Repositories\Repository\User');
    }
}