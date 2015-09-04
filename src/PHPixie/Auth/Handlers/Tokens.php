<?php

namespace PHPixie\Auth\Handlers;

class Tokens
{
    protected $storageTypes = array(
        'database'
    );
    
    protected $handlers;
    protected $database;
    
    public function __construct($handlers, $database)
    {
        $this->handlers = $handlers;
        $this->database = $database;
    }
    
    public function token($series, $userId, $challenge, $expires, $passphrase = null)
    {
        return new Tokens\Token($series, $userId, $challenge, $expires, $passphrase);
    }
    
    public function handler($configData)
    {
        return new Tokens\Handler(
            $this,
            $this->handlers->random(),
            $configData
        );
    }
        
    public function sqlStorage($connection, $configData)
    {
        return new Tokens\Storage\Database\SQL(
            $this,
            $connection,
            $configData
        );
    }
    
    public function mongoStorage($connection, $configData)
    {
        return new Tokens\Storage\Database\Mongo(
            $this,
            $connection,
            $configData
        );
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
        $connectionName = $configData->get('connection', 'default');
        $connection = $this->database->get($connectionName);
        
        if($connection instanceof \PHPixie\Database\Type\SQL\Connection) {
            return $this->sqlStorage($connection, $configData);
        }
        
        if($connection instanceof \PHPixie\Database\Driver\Mongo\Connection) {
            return $this->mongoStorage($connection, $configData);
        }
        
        $class = get_class($connection);
        throw new \PHPixie\Auth\Exception("No storage for the '$class' connection");
    }
}