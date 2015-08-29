<?php

namespace PHPixie\Auth\Persistance\Storage\Database;

class SQL extends \PHPixie\Auth\Handlers\Tokens\Storage\Database
{
    protected $table;
    
    public function __construct($connection, $configData)
    {
        parent::__construct($connection);
        $this->table = $configData->getRequired('table');
    }
    
    protected function setSource($query)
    {
        $query->table($this->table);
    }
}