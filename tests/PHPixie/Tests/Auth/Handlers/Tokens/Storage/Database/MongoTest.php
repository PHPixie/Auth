<?php

namespace PHPixie\Tests\Auth\Handlers\Tokens\Storage\Database;

/**
 * @coversDefaultClass \PHPixie\Auth\Handlers\Tokens\Storage\Database\Mongo
 */
class MongoTest extends \PHPixie\Tests\Auth\Handlers\Tokens\Storage\DatabaseTest
{
    protected $configData;
    protected $collection = 'pixie';
    
    public function setUp()
    {
        $this->configData = $this->quickMock('\PHPixie\Slice\Data');
        $this->method($this->configData, 'getRequired', $this->collection, array('collection'), 0);
        
        parent::setUp();
    }
    
    protected function prepareSetSource($query, $at)
    {
        $this->method($query, 'collection', null, array($this->collection), $at);
    }
    
    protected function getQuery($type)
    {
        return $this->quickMock('\PHPixie\Database\Driver\Mongo\Query\Type\\'.ucfirst($type));
    }
    
    protected function connection()
    {
        return $this->quickMock('\PHPixie\Database\Driver\Mongo\Connection');
    }
    
    protected function storage()
    {
        return new \PHPixie\Auth\Handlers\Tokens\Storage\Database\Mongo(
            $this->tokens,
            $this->connection,
            $this->configData
        );
    }
}