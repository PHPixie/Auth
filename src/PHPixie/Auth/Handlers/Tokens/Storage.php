<?php

namespace PHPixie\Auth\Handlers\Tokens\Storage;

interface Storage
{
    public function insert($token, $expires);
    public function get($series);
    public function update($series, $challenge, $expires);
    public function remove($series);
}