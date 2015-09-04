<?php

namespace PHPixie\Auth\Handlers\Tokens\Storage\Database;

class SQL extends \PHPixie\Auth\Handlers\Tokens\Storage\Database
{
    protected $table;
    
    public function __construct($tokens, $connection, $configData)
    {
        parent::__construct($tokens, $connection);
        $this->table = $configData->getRequired('table');
    }
    
    protected function setSource($query)
    {
        $query->table($this->table);
    }
}