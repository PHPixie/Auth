<?php

namespace PHPixie\Auth\Context\Container;

interface Settable
{
    /**
     * @param  \PHPixie\Auth\Context $authContext
     */
    public function setAuthContext($authContext);
}