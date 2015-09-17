<?php

namespace PHPixie\Auth\Providers;

interface Builder
{
    public function name();
    public function buildProvider($type, $domain, $name, $configData);
}