<?php

namespace Cloud\TceCloud\Contracts;

interface Factory
{

    /**
     * Get an Cloud provider implementation.
     *
     * @param  string  $driver
     * @return Cloud\TceCloud\Contracts\Provider
     */
    public function driver($driver = null);
}
