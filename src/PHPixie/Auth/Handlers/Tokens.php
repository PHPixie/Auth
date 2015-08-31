<?php

namespace PHPixie\Auth\Handlers;

class Tokens
{
    protected $storageTypes = array(
        'database'
    );
    
    public function token($userId, $series, $challenge)
    {
        return new Tokens\Token($userId, $series, $challenge);
    }
    
    protected function handler($storage)
    {
        return new Tokens\Hander(
            $this->handlers->random(),
            $storage
        );
    }
        
    public function sqlStorage($connection, $configData)
    {
        return new Tokens\Storage\Database\SQL(
            $connection,
            $configData
        );
    }
    
    public function mongoStorage($connection, $configData)
    {
        return new Tokens\Storage\Database\Mongo(
            $connection,
            $configData
        );
    }
    
    public function buildHandler($configData)
    {
        $type = $configData->get('type', 'database');
        $method = 'build'.$type.'Storage';
        return $this->$method($configData);
    }
    
    public function buildStorage($configData)
    {
        $type = $configData->get('type', 'database');
        if(!in_array($type, $this->storageTypes)) {
            throw new \PHPixie\Auth\Exception("Token storage type '$type' does not exist");
        }
        
        $method = $type.'Storage';
        return $this->$method($configData);
    }  
    
    public function databaseStorage($configData)
    {
        $connection = $this->database->get($connectionName);
        
        if($connection instanceof \PHPixie\Database\Type\SQL\Connection) {
            return new $this->sqlDatabase($connection, $configData);
        }
        
        if($connection instanceof \PHPixie\Database\Driver\Mongo\Connection) {
            return new $this->mongoDatabase($connection, $configData);
        }
        
        $class = get_class($connection);
        throw new \PHPixie\Auth\Exception("No storage for the '$class' connection");
    }
}