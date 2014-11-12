<?php

namespace PHPixie\Auth\Repository;

class ORM extends \PHPixie\Auth\Repository
          implements \PHPixie\Auth\Login\Password\Repository
{
    protected $login_field;
    protected $token_field;
    
    public function __construct($pixie, $service, $config_prefix)
    {
        parent::__construct($pixie, $service, $config_prefix);
        
        $this->login_field = $this->pixie->config->get($this->config_prefix."login_field");
        $this->token_field = $this->pixie->config->get($this->config_prefix."login_token_field", null);
    }
    public function get_by_login($login)
    {
        $login_field = $this->login_field;
        return $this->service->user_model()
						->where($login_field, $login)
						->find();
    }
    
    public function save_login_token($user, $token)
    {
        $token_field = $this->token_field;
        $user->$token_field = $token;
		$user->save();
    }
}