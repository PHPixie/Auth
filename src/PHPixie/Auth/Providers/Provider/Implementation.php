<?php

namespace PHPixie\Auth\Providers\Provider;

abstract class Implementation implements \PHPixie\Auth\Providers\Provider
{
    /**
     * @var \PHPixie\Auth\Domains\Domain
     */
    protected $domain;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \PHPixie\Slice\Type\ArrayData
     */
    protected $configData;

    /**
     * @param \PHPixie\Auth\Domains\Domain  $domain
     * @param string                        $name
     * @param \PHPixie\Slice\Type\ArrayData $configData
     */
    public function __construct($domain, $name, $configData)
    {
        $this->domain     = $domain;
        $this->name       = $name;
        $this->configData = $configData;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @return \PHPixie\AuthLogin\Repository
     */
    protected function repository()
    {
        return $this->domain->repository();
    }
}