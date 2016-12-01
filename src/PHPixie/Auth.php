<?php

namespace PHPixie;

use PHPixie\Auth\Domains\Domain;

class Auth
{
    /**
     * @var Auth\Builder
     */
    protected $builder;

    /**
     * @param \PHPixie\Slice\Type\ArrayData $configData
     * @param \PHPixie\Bundles\Auth         $repositoryRegistry
     * @param Auth\Providers\Builder[]      $providerBuilders
     * @param \PHPixie\Framework\Context    $contextContainer
     */
    public function __construct(
        $configData,
        $repositoryRegistry = null,
        $providerBuilders   = array(),
        $contextContainer   = null
    )
    {
        $this->builder = $this->buildBuilder(
            $configData,
            $repositoryRegistry,
            $providerBuilders,
            $contextContainer
        );
    }

    /**
     * @return Auth\Domains
     */
    public function domains()
    {
        return $this->builder->domains();
    }

    /**
     * @param string $name
     * @return Domain
     */
    public function domain($name = 'default')
    {
        return $this->builder->domains()->get($name);
    }

    /**
     * @return Auth\Context
     */
    public function context()
    {
        return $this->builder->context();
    }

    /**
     * @return Auth\Context
     */
    public function buildContext()
    {
        return $this->builder->buildContext();
    }

    /**
     * @return Auth\Builder
     */
    public function builder()
    {
        return $this->builder;
    }

    /**
     * @param \PHPixie\Slice\Type\ArrayData $configData
     * @param \PHPixie\Bundles\Auth         $repositoryRegistry
     * @param Auth\Providers\Builder[]      $providerBuilders
     * @param \PHPixie\Framework\Context    $contextContainer
     * @return Auth\Builder
     */
    protected function buildBuilder(
        $configData,
        $repositoryRegistry,
        $providerBuilders,
        $contextContainer
    )
    {
        return new Auth\Builder(
            $configData,
            $repositoryRegistry,
            $providerBuilders,
            $contextContainer
        );
    }
}