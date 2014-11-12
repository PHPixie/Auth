<?php

namespace PHPixie\Auth\Login\Password;

/**
 * Password user repository
 */
interface Repository
{
    /**
     * Gets user by login
     *
     * @param string $login user login
     * 
     * @return \PHPixie\Auth\Login\Password\User
     */
    public function get_by_login($login);
    
    /**
     * Saves login token for user
     *
     * @param \PHPixie\Auth\Login\Password\User $user User
     * @param string $token Login token
     * 
     * @return \PHPixie\Auth\Login\Password\User
     */
    public function save_login_token($user, $token);
}