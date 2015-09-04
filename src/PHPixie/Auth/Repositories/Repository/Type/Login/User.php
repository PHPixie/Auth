<?php

namespace PHPixie\Auth\Repositories\Repository\Type\Login;

interface User extends \PHPixie\Auth\Repositories\Repository\User
{
    public function passwordHash();
}