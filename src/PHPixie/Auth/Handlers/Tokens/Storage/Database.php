<?php

namespace PHPixie\Auth\Handlers\Tokens\Storage;

abstract class Database implements \PHPixie\Auth\Persistance\Storage
{
    protected $connection;
    
    public function __construct($connection)
    {
        $this->connection = $connection;
    }
    
    public function insert($token, $expires)
    {
        $query = $this->connection()->insertQuery();
        $this->setSource($query);
        
        $query
            ->data(array(
                'series'    => $token->series(),
                'userId'    => $token->userId(),
                'challenge' => $token->challenge(),
                'expires'   => $expires
            ))
            ->execute();
    }
    
    public function get($series)
    {
        $query = $this->connection()->insertQuery();
        $this->setSource($query);
        
        $query
            ->table($this->data)
            ->where('expires', '<', time())
            ->execute();
        
        $query = $this->connection()->selectQuery();
        $this->setSource($query);
        
        $data = $query
            ->table($this->table)
            ->where('series', $series)
            ->execute()
            ->current();
        
        if($data === null) {
            return null;
        }
        
        return $this->persistance->token(
            $data->series,
            $data->userId,
            $data->challenge
        );
    }
    
    public function update($series, $challenge, $expires)
    {
        $query = $this->connection()->updateQuery();
        $this->setSource($query);
        
        $query
            ->table($this->table)
            ->set(array(
                'challenge' => $challenge,
                'expires'   => $expires
            ))
            ->where('series', $series)
            ->execute();
    }
    
    public function remove($series)
    {
        $query = $this->connection()->deleteQuery();
        $this->setSource($query);
        
        $query
            ->table($this->table)
            ->where('series', $series)
            ->execute();
    }
    
    protected abstract function setSource($query);
}