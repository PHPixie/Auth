<?php

namespace PHPixie\Auth\Repositories\Repository\Type;

interface Login extends \PHPixie\Auth\Repositories\Repository
{
    public function getByLogin($login);
}