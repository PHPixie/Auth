<?php

namespace PHPixie\Auth\Providers;

interface Builder
{
    public function name();
    public function buildFromConfig($type, $domain, $name, $configData);
}