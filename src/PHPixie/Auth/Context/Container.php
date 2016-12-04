<?php

namespace PHPixie\Auth\Context;

interface Container
{
    /**
     * @return \PHPixie\Auth\Context
     */
    public function authContext();
}