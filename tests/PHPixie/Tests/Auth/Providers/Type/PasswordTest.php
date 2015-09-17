<?php

namespace PHPixie\Tests\Auth\Providers\Type;

/**
 * @coversDefaultClass \PHPixie\Auth\Providers\Type\Password
 */
class PasswordTest extends \PHPixie\Tests\Auth\Providers\Provider\ImplementationTest
{
    protected $name = 'password';
    
    protected $passwordHandler;
    
    public function setUp()
    {
        $this->passwordHandler = $this->quickMock('\PHPixie\Security\Password');
        
        parent::setUp();
    }
    
    /**
     * @covers ::hash
     * @covers ::<protected>
     */
    public function testHash()
    {
        $this->method($this->passwordHandler, 'hash', 'pixie', array('trixie'), 0);
        $this->assertSame('pixie', $this->provider->hash('trixie'));
    }
    
    /**
     * @covers ::login
     * @covers ::<protected>
     */
    public function testLogin()
    {
        $this->loginTest();
        $this->loginTest(true);
        $this->loginTest(true, true);
    }
    
    protected function loginTest($userExists = false, $passwordIsValid = false)
    {
        $expects = $this->prepareLoginTest('pixie', 'trixie', $userExists, $passwordIsValid);
        $this->assertSame($expects, $this->provider->login('pixie', 'trixie'));
    }
    
    protected function prepareLoginTest($login, $password, $userExists, $passwordIsValid)
    {
        $domainAt = 0;
        $repository = $this->prepareRepository($domainAt);
        
        $user = $userExists ? $this->getUser() : null;
        $this->method($repository, 'getByLogin', $user, array($login), 0);
        
        if(!$userExists) {
            return null;
        }
        
        $this->method($user, 'passwordHash', 'hashed', array(), 0);
        $this->method($this->passwordHandler, 'verify', $passwordIsValid, array($password, 'hashed'), 0);
        
        if(!$passwordIsValid) {
            return null;
        }
        
        $this->method($this->domain, 'setUser', null, array($user, $this->name), $domainAt++);
        
        $providers = array('cookie', 'session');
        $this->method($this->configData, 'get', $providers, array('persistProviders', array()), 0);
        
        foreach($providers as $name) {
            $provider = $this->quickMock('\PHPixie\Auth\Providers\Provider\Persistent');
            $this->method($this->domain, 'provider', $provider, array($name), $domainAt++);
            
            $this->method($provider, 'persist', null, array(), 0);
        }
        
        return $user;
    }
    
    protected function getRepository()
    {
        return $this->quickMock('\PHPixie\Auth\Repositories\Repository\Type\Login');
    }
    
    protected function getUser()
    {
        return $this->quickMock('\PHPixie\Auth\Repositories\Repository\Type\Login\User');
    }
    
    protected function provider()
    {
        return new \PHPixie\Auth\Providers\Type\Password(
            $this->passwordHandler,
            $this->domain,
            $this->name,
            $this->configData
        );
    }
}