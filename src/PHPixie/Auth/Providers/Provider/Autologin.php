<?php

namespace PHPixie\Auth\Providers\Provider;

interface Autologin extends \PHPixie\Auth\Providers\Provider
{
    /**
     * @return \PHPixie\AuthLogin\Repository\User
     */
    public function check();
}