<?php

namespace PHPixie\Auth\Repositories;

interface Registry
{
    /**
     * @param string $name
     * @return \PHPixie\AuthLogin\Repository
     */
    public function repository($name);
}