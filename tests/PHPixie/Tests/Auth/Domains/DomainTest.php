<?php

namespace PHPixie\Tests\Auth\Domains;

/**
 * @coversDefaultClass \PHPixie\Auth\Domains\Domain
 */
class DomainTest extends \PHPixie\Test\Testcase
{
    protected $builder;
    protected $name = 'pixie';
    protected $configData;
    
    protected $domain;
    
    protected $providers = array();
    
    public function setUp()
    {
        $this->builder    = $this->quickMock('\PHPixie\Auth\Builder');
        $this->configData = $this->getSliceData();
        
        $this->domain = new \PHPixie\Auth\Domains\Domain(
            $this->builder,
            $this->name,
            $this->configData
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
     * @covers ::name
     * @covers ::<protected>
     */
    public function testName()
    {
        $this->assertSame($this->name, $this->domain->name());
    }
    
    /**
     * @covers ::providers
     * @covers ::<protected>
     */
    public function testProviders()
    {
        $providers = $this->prepareRequireProviders(array(
            'pixie'  => $this->getProvider(),
            'trixie' => $this->getProvider(),
        ));
        
        
        for($i=0; $i<2; $i++) {
            $this->assertSame($providers, $this->domain->providers());
        }
    }
    
    /**
     * @covers ::provider
     * @covers ::<protected>
     */
    public function testProvider()
    {
        $providers = $this->prepareRequireProviders(array(
            'pixie'  => $this->getProvider(),
            'trixie' => $this->getProvider(),
        ));
        
        foreach($providers as $name => $provider) {
            $this->assertSame($provider, $this->domain->provider($name));
        }
    }
    
    /**
     * @covers ::repository
     * @covers ::<protected>
     */
    public function testRepository()
    {
        $this->method($this->configData, 'getRequired', 'pixie', array('repository'), 0);
        
        $repositories = $this->quickMock('\PHPixie\Auth\Repositories');
        $this->method($this->builder, 'repositories', $repositories, array(), 0);
        
        $repository = $this->quickMock('\PHPixie\Auth\Repositories\Repository');
        $this->method($repositories, 'get', $repository, array(), 0);
        
        for($i=0; $i<2; $i++) {
            $this->assertSame($repository, $this->domain->repository());
        }
    }
    
    /**
     * @covers ::user
     * @covers ::<protected>
     */
    public function testUser()
    {
        $user = $this->getUser();
        $this->prepareUser($user);
        
        $this->assertSame($user, $this->domain->user());
    }
    
    /**
     * @covers ::requireUser
     * @covers ::<protected>
     */
    public function testRequireUser()
    {
        $user = $this->getUser();
        $this->prepareUser($user);
        $this->assertSame($user, $this->domain->requireUser());
        
        $this->prepareUser(null);
        $domain = $this->domain;
        $this->assertException(function() use($domain){
            $domain->requireUser();
        },'\PHPixie\Auth\Exception');
    }
    
    /**
     * @covers ::setUser
     * @covers ::<protected>
     */
    public function testSetUser()
    {
        $context = $this->prepareContext();
        
        $user = $this->getUser();
        $this->method($context, 'setUser', null, array($user, $this->name, 'pixie'), 0);
        
        $this->domain->setUser($user, 'pixie');
    }
    
    /**
     * @covers ::unsetUser
     * @covers ::<protected>
     */
    public function testUnsetUser()
    {
        $this->prepareUnsetUser();
        $this->domain->unsetUser();
    }
    
    /**
     * @covers ::checkUser
     * @covers ::<protected>
     */
    public function testCheckUser()
    {
        $builderAt = 0;
        $this->prepareUnsetUser($builderAt);
        
        $providers = $this->prepareRequireProviders(
            array(
                'pixie'  => $this->getProvider(),
                'trixie' => $this->getAutologinProvider(),
                'blum'   => $this->getAutologinProvider(),
                'stella' => $this->getAutologinProvider(),
            ),
            $builderAt
        );
        
        $this->method($providers['trixie'], 'check', null, array(), 0);
        
        $user = $this->getUser();
        $this->method($providers['blum'], 'check', $user, array(), 0);
        
        $providers['stella']
            ->expects($this->never())
            ->method('check');
        
        $this->assertSame($user, $this->domain->checkUser());
    }
    
    /**
     * @covers ::forgetUser
     * @covers ::<protected>
     */
    public function testForgetUser()
    {
        $builderAt = 0;
        $this->prepareUnsetUser($builderAt);
        
        $providers = $this->prepareRequireProviders(
            array(
                'pixie'  => $this->getProvider(),
                'trixie' => $this->getPersistentProvider(),
                'blum'   => $this->getPersistentProvider(),
            ),
            $builderAt
        );
        
        foreach(array('trixie', 'blum') as $name) {
            $this->method($providers[$name], 'forget', null, array(), 0);
        }
        
        $this->domain->forgetUser();
    }
    
    protected function prepareContext(&$builderAt = 0)
    {
        $contextContainer = $this->quickMock('\PHPixie\Auth\Context\Container');
        $this->method($this->builder, 'contextContainer', $contextContainer, array(), $builderAt++);
        
        $context = $this->quickMock('\PHPixie\Auth\Context');
        $this->method($contextContainer, 'authContext', $context, array(), 0);
        
        return $context;
    }
    
    protected function prepareUser($user)
    {
        $context = $this->prepareContext();
        $this->method($context, 'user', $user, array($this->name), 0);
    }
    
    protected function prepareUnsetUser(&$builderAt = 0)
    {
        $context = $this->prepareContext($builderAt);
        $this->method($context, 'unsetUser', null, array(), 0);
    }
    
    protected function prepareRequireProviders($providers, &$builderAt = 0)
    {
        $providerBuilder = $this->quickMock('\PHPixie\Auth\Providers');
        $this->method($this->builder, 'providers', $providerBuilder, array(), $builderAt++);
        
        $providersConfig = $this->getSliceData();
        $this->method($this->configData, 'slice', $providersConfig, array('providers'), 0);
        
        $configAt  = 0;
        $this->method($providersConfig, 'keys', array_keys($providers), array(), $configAt++);
        
        $providersAt = 0;
        foreach($providers as $name => $provider) {
            $providerConfig = $this->getSliceData();
            $this->method($providersConfig, 'slice', $providerConfig, array($name), $configAt++);
            
            $this->method($providerBuilder, 'buildFromConfig', $provider, array(
                $this->domain,
                $name,
                $providerConfig
            ), $providersAt++);
        }
        
        return $providers;
    }
    
    protected function getSliceData()
    {
        return $this->quickMock('\PHPixie\Slice\Data');
    }
    
    protected function getProvider()
    {
        return $this->quickMock('\PHPixie\Auth\Providers\Provider');
    }
    
    protected function getAutologinProvider()
    {
        return $this->quickMock('\PHPixie\Auth\Providers\Provider\Autologin');
    }
    
    protected function getPersistentProvider()
    {
        return $this->quickMock('\PHPixie\Auth\Providers\Provider\Persistent');
    }
    
    protected function getUser()
    {
        return $this->quickMock('\PHPixie\Repositories\Repository\User');
    }
}