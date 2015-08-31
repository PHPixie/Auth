<?php

namespace PHPixie\Auth\Providers\Provider;

interface Persistent extends Autologin
{
    public function persist();
    public function forget();
}