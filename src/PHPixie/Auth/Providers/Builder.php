<?php

namespace PHPixie\Auth\Providers;

interface Builder
{
    /**
     * @return string
     */
    public function name();

    /**
     * @param string                        $type
     * @param \PHPixie\Auth\Domains\Domain  $domain
     * @param string                        $name
     * @param \PHPixie\Slice\Type\ArrayData $configData
     * @return \PHPixie\Auth\Providers\Provider
     */
    public function buildProvider($type, $domain, $name, $configData);
}