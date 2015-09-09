<?php

namespace PHPixie\Tests\Auth\Providers\Type;

/**
 * @coversDefaultClass \PHPixie\Auth\Providers\Type\Session
 */
class SessionTest extends \PHPixie\Tests\Auth\Providers\Provider\ImplementationTest
{
    protected $name = 'session';
    
    protected $httpContextContainer;
    
    protected $key = 'user_id';
    
    public function setUp()
    {
        $this->httpContextContainer = $this->quickMock('\PHPixie\HTTP\Context\Container');

        parent::setUp();
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