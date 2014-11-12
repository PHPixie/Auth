<?php

namespace PHPixie\Auth\Login\Password;

/**
 * Password User Interface
 */
interface User extends \PHPixie\Auth\User
{
    /**
     * Gets users password hash
     *
     * @return string
     */
    public function password_hash();
    
    /**
     * Gets users login token
     *
     * @return string
     */
    public function login_token();
}