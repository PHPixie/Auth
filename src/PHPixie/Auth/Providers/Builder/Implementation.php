<?php

namespace PHPixie\Auth\Providers\Builder;

abstract class Implementation implements \PHPixie\Auth\Providers\Builder
{
    /**
     * @param string                        $type
     * @param \PHPixie\Auth\Domains\Domain  $domain
     * @param string                        $name
     * @param \PHPixie\Slice\Type\ArrayData $configData
     * @return \PHPixie\Auth\Providers\Provider
     */
    public function buildProvider($type, $domain, $name, $configData)
    {
        $method = 'build'.ucfirst($type).'Provider';
        return $this->$method($domain, $name, $configData);
    }

    /**
     * @return string
     */
    abstract public function name();
}