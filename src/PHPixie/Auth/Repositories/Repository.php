<?php

namespace PHPixie\Auth\Repositories;

interface Repository
{
    /**
     * @param $id
     * @return \PHPixie\AuthLogin\Repository\User
     */
    public function getById($id);
}