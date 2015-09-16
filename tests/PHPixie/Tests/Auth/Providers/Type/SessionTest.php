<?php

namespace PHPixie\Tests\Auth\Providers\Type;

/**
 * @coversDefaultClass \PHPixie\Auth\Providers\Type\Session
 */
class SessionTest extends \PHPixie\Tests\Auth\Providers\Provider\ImplementationTest
{
    protected $name = 'session';
    protected $httpContextContainer;
    
    protected $sessionKey = 'userId';
    protected $session;
    
    public function setUp()
    {
        $this->httpContextContainer = $this->quickMock('\PHPixie\HTTP\Context\Container');
        
        parent::setUp();
        
        $context = $this->quickMock('\PHPixie\HTTP\Context');
        $this->method($this->httpContextContainer, 'httpContext', $context, array());
        
        $this->session = $this->quickMock('\PHPixie\HTTP\Context\Session');
        $this->method($context, 'session', $this->session, array());
    }
    
    /**
     * @covers ::check
     * @covers ::<protected>
     */
    public function testCheck()
    {
        $this->prepareSessionKey();
        
        $this->checkTest();
        $this->checkTest(true);
        $this->checkTest(true, true);
    }
    
    protected function checkTest($keyExists = false, $userExists = false)
    {
        $expect = null;
        
        $userId = $keyExists ? 7 : null;
        $this->method($this->session, 'get', $userId, array($this->sessionKey), 0);
        
        if($keyExists) {
            $repository = $this->prepareRepository();
            $user = $userExists ? $this->getUser() : null;
            $this->method($repository, 'getById', $user, array($userId), 0);
            
            if($userExists) {
                $expect = $user;
                $this->method($this->domain, 'setUser', null, array($user, $this->name), 1);
            }else{
                $this->prepareForget(1);
            }
        }
        
        $this->assertSame($expect, $this->provider->check());
    }
    
    /**
     * @covers ::persist
     * @covers ::<protected>
     */
    public function testPersist()
    {
        $user = $this->getUser();
        $this->method($this->domain, 'requireUser', $user, array(), 0);
        $this->method($user, 'id', 7, array(), 0);
        
        $this->prepareSessionKey(1);
        $this->method($this->session, 'set', null, array($this->sessionKey, 7), 0);
        
        $this->provider->persist();
    }
    
    /**
     * @covers ::forget
     * @covers ::<protected>
     */
    public function testForget()
    {
        $this->prepareSessionKey();
        $this->prepareForget();
        $this->provider->forget();
    }
    
    protected function prepareForget($sessionAt = 0)
    {
        $this->method($this->session, 'remove', null, array($this->sessionKey), $sessionAt);    
    }
            
    protected function prepareSessionKey($domainAt = 0)
    {
        $this->method($this->domain, 'name', 'pixie', array(), $domainAt);
        $this->method($this->configData, 'get', $this->sessionKey, array('key', 'pixieUserId'), 0);
    }
    
    protected function provider()
    {
        return new \PHPixie\Auth\Providers\Type\Session(
            $this->httpContextContainer,
            $this->domain,
            $this->name,
            $this->configData
        );
    }
}