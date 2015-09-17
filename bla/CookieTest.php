<?php

namespace PHPixie\Tests\Auth\Providers\Type;

/**
 * @coversDefaultClass \PHPixie\Auth\Providers\Type\Cookie
 */
class CookieTest extends \PHPixie\Tests\Auth\Providers\Provider\ImplementationTest
{
    protected $name = 'cookie';
    protected $tokens;
    protected $httpContextContainer;
    
    protected $cookieName = 'userId';
    protected $tokenHandler;
    protected $cookies;
    
    public function setUp()
    {
        $this->tokens = $this->quickMock('\PHPixie\Security\Tokens');
        $this->httpContextContainer = $this->quickMock('\PHPixie\HTTP\Context\Container');
        
        parent::setUp();
        
        $context = $this->quickMock('\PHPixie\HTTP\Context');
        $this->method($this->httpContextContainer, 'httpContext', $context, array());
        
        $this->tokenHandler = $this->quickMock('\PHPixie\Security\Tokens\Handler');
        
        $this->cookies = $this->quickMock('\PHPixie\HTTP\Context\Cookies');
        $this->method($context, 'cookies', $this->cookies, array());
    }
    
    /**
     * @covers ::check
     * @covers ::<protected>
     */
    public function testCheck()
    {
        $this->checkTest(true, true, true, true, true);
        $this->checkTest(true, true, true, false, false);
        $this->checkTest(true, true, false, false, false);
        $this->checkTest(true, false, false, false, false);
        $this->checkTest(false, false, false, false, false);
    }
    
    protected function checkTest($cookieExists, $tokenExists, $userExists, $refresh, $isFirst)
    {
        $expects = $this->prepareCheckTest(
            $cookieExists,
            $tokenExists,
            $userExists,
            $refresh,
            $isFirst
        );
        $this->assertSame($expects, $this->provider->check());
    }
    
    protected function prepareCheckTest($cookieExists, $tokenExists, $userExists, $refresh, $isFirst)
    {
        $configAt  = 0;
        $domainAt  = 0;
        $cookiesAt = 0;
        $handlerAt = 0;
        
        $expects = null;
        
        if($isFirst) {
            $this->prepareCookieName($configAt, $domainAt);
        }
        
        $encodedToken = $cookieExists ? 'pixie' : null;
        $this->prepareGetCookie($encodedToken, $cookiesAt);
        
        if(!$cookieExists) {
            return null;
        }
        
        if($isFirst) {
            $this->prepareTokenHandler($configAt);
        }
            
        $token = $tokenExists ? $this->getToken() : null;
        $this->method($this->tokenHandler, 'getByString', $token, array($encodedToken), $handlerAt++);
            
        if(!$tokenExists) {
            $this->prepareUnsetCookie($cookiesAt);
            return null;
        }
        
        $this->method($token, 'userId', 5, array(), 0);
        $repository = $this->prepareRepository($domainAt);
                
        $user = $userExists ? $this->getUser() : null;
        $this->method($repository, 'getById', $user, array(5), 0);
                
        if(!$userExists) {
            $this->prepareRemoveToken($encodedToken, $handlerAt);
            $this->prepareUnsetCookie($cookiesAt);
            return null;
        }
        
        $this->method($this->configData, 'get', $refresh, array('refresh', true), $configAt++);
        
        if($refresh) {
            $newToken = $this->getToken();
            $this->method($this->tokenHandler, 'refresh', $newToken, array($token), $handlerAt++);
            $this->prepareSetCookie($newToken, $cookiesAt);
        }   
        
        $providers = array('pixie', 'session');
        $this->method($this->configData, 'get', $providers, array('persistProviders', array()), $configAt++);
        
        foreach($providers as $name) {
            $provider = $this->quickMock('\PHPixie\Auth\Providers\Provider\Persistent');
            $this->method($this->domain, 'provider', $provider, array($name), $domainAt++);
            
            $this->method($provider, 'persist', null, array(), 0);
        }
        
        $this->method($this->domain, 'setUser', null, array($user, $this->name), $domainAt++);
        
        return $user;
    }

    /**
     * @covers ::persist
     * @covers ::<protected>
     */
    public function testPersist()
    {
        $this->persistTest(false, true);
        $this->persistTest(true);
    }
    
    protected function persistTest($withLifetime = true, $isFirst = false)
    {
        $configAt = 0;
        $domainAt = 0;
        
        $lifetime = 100;
        
        if(!$withLifetime) {
            $this->method($this->configData, 'get', $lifetime, array('defaultLifetime'), $configAt++);
        }
        
        $user = $this->getUser();
        $this->method($this->domain, 'requireUser', $user, array(), $domainAt++);
        
        if($isFirst) {
            $this->prepareTokenHandler($configAt);
        }
        
        $token = $this->getToken();
        $this->method($this->tokenHandler, 'create', $token, array($user, $lifetime), 0);
        
        if($isFirst) {
            $this->prepareCookieName($configAt, $domainAt);
        }
        
        $this->prepareSetCookie($token);
        
        if($withLifetime) {
            $this->provider->persist($lifetime);
        }else{
            $this->provider->persist();
        }
    }
    
    /**
     * @covers ::forget
     * @covers ::<protected>
     */
    public function testForget()
    {
        $this->forgetTest(false, true);
        $this->forgetTest(true);
    }
    
    protected function forgetTest($tokenExists = false, $isFirst = false)
    {
        $cookiesAt = 0;
        $configAt = 0;
        
        if($isFirst) {
            $this->prepareCookieName($configAt);
            $this->prepareTokenHandler($configAt);
        }
        
        $token = $tokenExists ? 'pixie' : null;
        $this->prepareGetCookie($token, $cookiesAt);
        
        if($tokenExists) {
            $this->prepareUnsetCookie($cookiesAt);
            $this->prepareRemoveToken('pixie');
        }
        
        $this->provider->forget();
    }
    
    protected function prepareTokenHandler(&$configAt = 0, &$tokensAt = 0)
    {
        $tokenConfig = $this->getSliceData();
        $this->method($this->configData, 'slice', $tokenConfig, array('tokens'), $configAt++);
        
        $this->method($this->tokens, 'handler', $this->tokenHandler, array($tokenConfig), $tokensAt);
    }
    
    protected function prepareCookieName(&$configAt = 0, &$domainAt = 0)
    {
        $this->method($this->domain, 'name', 'pixie', array(), $domainAt++);
        $this->method($this->configData, 'get', $this->cookieName, array('cookie', 'pixieToken'), $configAt++);
    }
    
    protected function prepareSetCookie($token, &$cookiesAt = 0)
    {
        $this->method($token, 'string', 'pixie', array(), 0);
        $this->method($token, 'expires', 100, array(), 1);
        
        $self       = $this;
        $cookieName = $this->cookieName;
        $this->method($this->cookies, 'set', function() use($self, $cookieName) {
            $args = func_get_args();
            
            $self->assertSame($cookieName, $args[0]);
            $self->assertSame('pixie', $args[1]);
            $self->assertTrue($args[2]+time()-100 <= 1);
        }, null, $cookiesAt++);
    }
    
    protected function prepareUnsetCookie(&$cookiesAt = 0)
    {
        $this->method($this->cookies, 'remove', null, array($this->cookieName), $cookiesAt++);
    }
    
    protected function prepareGetCookie($value, &$cookiesAt = 0)
    {
        $this->method($this->cookies, 'get', $value, array($this->cookieName), $cookiesAt++);
    }
    
    protected function prepareRemoveToken($encodedToken, &$handlerAt = 0)
    {
        $this->method($this->tokenHandler, 'removeByString', null, array($encodedToken), $handlerAt);
    }
    
    protected function getToken()
    {
        return $this->quickMock('\PHPixie\Security\Tokens\Token');
    }
    
    protected function provider()
    {
        return new \PHPixie\Auth\Providers\Type\Cookie(
            $this->tokens,
            $this->httpContextContainer,
            $this->domain,
            $this->name,
            $this->configData
        );
    }
}