<?php

namespace PHPixie\Auth\Handlers\Tokens\Storage;

abstract class Database implements \PHPixie\Auth\Handlers\Tokens\Storage
{
    protected $tokens;
    protected $connection;
    
    public function __construct($tokens, $connection)
    {
        $this->tokens = $tokens;
        $this->connection = $connection;
    }
    
    public function insert($token)
    {
        $query = $this->connection->insertQuery();
        $this->setSource($query);
        
        $query
            ->data(array(
                'series'    => $token->series(),
                'userId'    => $token->userId(),
                'challenge' => $token->challenge(),
                'expires'   => $token->expires()
            ))
            ->execute();
    }
    
    public function get($series)
    {
        $query = $this->connection->deleteQuery();
        $this->setSource($query);
        
        $query
            ->where('expires', '<', time());
            //->execute();
        
        $query = $this->connection->selectQuery();
        $this->setSource($query);
        
        $data = $query
            ->where('series', $series)
            ->execute()
            ->current();
        
        if($data === null) {
            return null;
        }
        
        return $this->tokens->token(
            $data->series,
            $data->userId,
            $data->challenge,
            $data->expires
        );
    }
    
    public function update($token)
    {
        $query = $this->connection->updateQuery();
        $this->setSource($query);
        
        $query
            ->table($this->table)
            ->set(array(
                'challenge' => $token->challenge(),
                'expires'   => $token->expires()
            ))
            ->where('series', $token->series())
            ->execute();
    }
    
    public function remove($series)
    {
        $query = $this->connection->deleteQuery();
        $this->setSource($query);
        
        $query
            ->table($this->table)
            ->where('series', $series)
            ->execute();
    }
    
    protected abstract function setSource($query);
}