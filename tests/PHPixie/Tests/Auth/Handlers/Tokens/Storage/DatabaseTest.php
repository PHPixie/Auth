<?php

namespace PHPixie\Tests\Auth\Handlers\Tokens\Storage;

/**
 * @coversDefaultClass \PHPixie\Auth\Handlers\Tokens\Storage\Database
 */
abstract class DatabaseTest extends \PHPixie\Test\Testcase
{
    protected $tokens;
    protected $connection;
    
    protected $storage;
    
    public function setUp()
    {
        $this->tokens     = $this->quickMock('\PHPixie\Auth\Handlers\Tokens');
        $this->connection = $this->connection();
        
        $this->storage = $this->storage();
    }
    
    /**
     * @covers \PHPixie\Auth\Handlers\Tokens\Storage\Database::__construct
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstruct()
    {
    
    }
    
    /**
     * @covers ::insert
     * @covers ::<protected>
     */
    public function testInsert()
    {
        $data = array(
            'series'    => 'pixie',
            'userId'    => 7,
            'challenge' => 'trixie',
            'expires'   => 18
        );
        
        $token = $this->token($data);
        
        $query = $this->getQuery('insert');
        $this->method($this->connection, 'insertQuery', $query, array(), 0);
        
        $this->prepareSetSource($query, 0);
        $this->method($query, 'data', $query, array($data), 1);
        $this->method($query, 'execute', null, array(), 2);
        
        $this->storage->insert($token);
    }
    
    /**
     * @covers ::update
     * @covers ::<protected>
     */
    public function testUpdate()
    {
        $token = $this->token(array(
            'series'    => 'pixie',
            'challenge' => 'trixie',
            'expires'   => 18
        ));
        
        $query = $this->getQuery('update');
        $this->method($this->connection, 'updateQuery', $query, array(), 0);
        
        $this->prepareSetSource($query, 0);
        $this->method($query, 'set', $query, array(array(
            'challenge' => 'trixie',
            'expires'   => 18
        )), 1);
        
        $this->method($query, 'where', $query, array('series', 'pixie'), 2);
        $this->method($query, 'execute', null, array(), 3);
        
        $this->storage->insert($token);
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
    
    abstract protected function prepareSetSource($query, $at);
    
    abstract protected function getQuery($type);
    abstract protected function connection();
    
    abstract protected function storage();
}