<?php

namespace PHPixie\Tests\Auth\Handlers\Tokens;

/**
 * @coversDefaultClass \PHPixie\Auth\Handlers\Tokens\Handler
 */
class HandlerTest extends \PHPixie\Test\Testcase
{
    protected $tokens;
    protected $random;
    protected $configData;
    
    protected $handler;
    
    protected $storage;
    protected $seriesLength;
    protected $passphraseLength;
    
    public function setUp()
    {
        $this->tokens = $this->quickMock('\PHPixie\Auth\Handlers\Tokens');
        $this->random = $this->quickMock('\PHPixie\Auth\Handlers\Random');
        
        $this->configData = $this->sliceData();
        
        $storageConfig = $this->sliceData();
        $this->method($this->configData, 'slice', $storageConfig, array('storage'), 0);
        
        $this->storage = $this->quickMock('\PHPixie\Auth\Handlers\Tokens\Storage');
        $this->method($this->tokens, 'buildStorage', $this->storage, array($storageConfig), 0);
        
        $this->seriesLength = 30;
        $this->method($this->configData, 'get', $this->seriesLength, array('seriesLength', 30), 1);
        
        $this->passphraseLength = 20;
        $this->method($this->configData, 'get', $this->passphraseLength, array('passphraseLength', 30), 2);
        
        $this->handler = new \PHPixie\Auth\Handlers\Tokens\Handler(
            $this->tokens,
            $this->random,
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
     * @covers ::create
     * @covers ::<protected>
     */
    public function testCreate()
    {
        $this->method($this->random, 'string', 'pixie', array($this->seriesLength), 0);
        $this->method($this->random, 'string', 'trixie', array($this->passphraseLength), 1);
        $challenge = $this->challenge('pixie', 'trixie');
        
        $user = $this->quickMock('\PHPixie\Auth\Repositories\Repository\User');
        $this->method($user, 'id', 5, array(), 0);
        
        $encoded = $this->encodeToken('pixie', 'trixie');
        
        $token = $this->getToken();
        $this->method($this->tokens, 'token', function() use($challenge, $encoded, $token) {
            $args = func_get_args();
            
            $this->assertTrue($args[3]-time()-100 <= 1);
            $this->assertSame(array(
                'pixie',
                5,
                $challenge,
                $args[3],
                $encoded
            ), $args);
            
            return $token;
        }, null, 0);
        
        $this->method($this->storage, 'insert', null, array($token), 0);
        $this->assertSame($token, $this->handler->create($user, 100));
    }
    
    /**
     * @covers ::getByString
     * @covers ::<protected>
     */
    public function testGetByString()
    {
        $this->getByStringTest();
        $this->getByStringTest(true);
        $this->getByStringTest(true, true);
        $this->getByStringTest(true, true, true);
    }
    
    protected function getByStringTest($isValid = false, $exists = false, $matches = false)
    {
        $expect = null;
        
        if(!$isValid) {
            $encoded = 'invalid';
        }else{
            $encoded = $this->encodeToken('pixie', 'trixie');
            if(!$exists) {
                $token = null;
            }else{
                $passphrase = $matches ? 'trixie' : 'blum';
                $token = $this->token(array(
                    'challenge' => $this->challenge('pixie', $passphrase)
                ));
                
                if($matches) {
                    $expect = $token;
                }
            }
            
            $this->method($this->storage, 'get', $token, array('pixie'), 0);
        }
        
        $this->assertSame($expect, $this->handler->getByString($encoded));
    }
    
    /**
     * @covers ::refresh
     * @covers ::<protected>
     */
    public function testRefresh()
    {
        $this->method($this->random, 'string', 'trixie', array($this->passphraseLength), 0);
        
        $token = $this->token(array(
            'series'   => 'pixie',
            'userId'   => 5,
            'expires' => 17
        ));
        
        $newToken = $this->getToken();
        $this->method($this->tokens, 'token', $newToken, array(
            'pixie',
            5,
            $this->challenge('pixie', 'trixie'),
            17,
            $this->encodeToken('pixie', 'trixie')
        ), 0);
        
        $this->method($this->storage, 'update', null, array($newToken), 0);
        
        $this->assertSame($newToken, $this->handler->refresh($token));
    }
    
    /**
     * @covers ::removeByString
     * @covers ::<protected>
     */
    public function testRemoveByString()
    {
        $this->handler->removeByString('test');
        
        $encoded = $this->encodeToken('pixie');
        $this->method($this->storage, 'remove', null, array('pixie'), 0);
        
        $this->handler->removeByString($encoded);
    }
    
    protected function encodeToken($series, $passphrase = 'trixie')
    {
        return "$series:$passphrase";
    }
    
    protected function challenge($series, $passphrase)
    {
        return md5($series.$passphrase);
    }
    
    protected function token($properties = array())
    {
        $token = $this->getToken();
        foreach($properties as $name => $value) {
            $this->method($token, $name, $value);
        }
        
        return $token;
    }
    
    protected function getToken()
    {
        return $this->quickMock('\PHPixie\Auth\Handlers\Tokens\Token');
    }
    
    protected function sliceData()
    {
        return $this->quickMock('\PHPixie\Slice\Data');
    }
}