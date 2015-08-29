<?php

namespace PHPixie\Auth\Persistance\Storage\Database;

class Mongo extends \PHPixie\Auth\Handlers\Tokens\Storage\Database
{
    protected $collection;
    
    public function __construct($connection, $configData)
    {
        parent::__construct($connection);
        $this->collection = $configData->getRequired('collection');
    }
    
    protected function setSource($query)
    {
        $query->collection($this->collection);
    }
}