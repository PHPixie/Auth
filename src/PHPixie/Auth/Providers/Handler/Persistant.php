<?php

namespace PHPixie\Auth\Handlers\Handler;

interface Persistent extends \PHPixie\Auth\Handlers\Handler
{
    public function check();
}