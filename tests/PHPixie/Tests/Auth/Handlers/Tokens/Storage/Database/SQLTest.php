<?php

namespace PHPixie\Tests\Auth\Handlers\Tokens\Storage\Database;

/**
 * @coversDefaultClass \PHPixie\Auth\Handlers\Tokens\Storage\Database\SQL
 */
class SQLTest extends \PHPixie\Tests\Auth\Handlers\Tokens\Storage\DatabaseTest
{
    protected $configData;
    protected $table = 'pixie';
    
    public function setUp()
    {
        $this->configData = $this->quickMock('\PHPixie\Slice\Data');
        $this->method($this->configData, 'getRequired', $this->table, array('table'), 0);
        
        parent::setUp();
    }
    
    protected function prepareSetSource($query, $at)
    {
        $this->method($query, 'table', null, array($this->table), $at);
    }
    
    protected function getQuery($type)
    {
        return $this->quickMock('\PHPixie\Database\Type\SQL\Query\Type\\'.ucfirst($type));
    }
    
    protected function connection()
    {
        return $this->quickMock('\PHPixie\Database\Type\SQL\Connection');
    }
    
    protected function storage()
    {
        return new \PHPixie\Auth\Handlers\Tokens\Storage\Database\SQL(
            $this->tokens,
            $this->connection,
            $this->configData
        );
    }
}