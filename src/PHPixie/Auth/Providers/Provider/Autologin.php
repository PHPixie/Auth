<?php

namespace PHPixie\Auth\Providers\Provider;

interface Persistent extends \PHPixie\Auth\Providers\Provider
{
    public function check();
}