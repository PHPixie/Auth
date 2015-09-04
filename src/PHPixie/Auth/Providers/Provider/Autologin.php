<?php

namespace PHPixie\Auth\Providers\Provider;

interface Autologin extends \PHPixie\Auth\Providers\Provider
{
    public function check();
}