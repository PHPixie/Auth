<?php

namespace PHPixie\Auth\Providers;

interface Provider
{
    /**
     * @return string
     */
    public function name();
}