<?php

namespace PHPixie\Auth\Handlers\Type;

class Password extends \PHPixie\Auth\Handlers\Handler
{
    protected $passwordHandler;
    
    public function __construct($domain, $passwordHandler, $configData)
    {
        parent::__construct($domain, $configData);
        $this->passwordHandler = $passwordHandler;
    }
    
    public function hash($password)
    {
        return $this->passwordHandler->hash($password);
    }
    
    public function login($login, $password)
    {
        $user = $this->userRepository()->getByLogin($login);
        
        if($user === null) {
            return null;
        }
        
        $hash = $user->passwordHash();
        if(!$this->passwordHandler->verify($password, $hash)) {
            return null;
        }
        
        $this->domain->setUser($user);
        return $user;
    }
}