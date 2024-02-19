<?php

namespace Cloud\TceCloud\Facades;

use Illuminate\Support\Facades\Facade;
use Cloud\TceCloud\Contracts\Factory;

/**
 * @method static Cloud\TceCloud\Contracts\Provider driver(string $driver = null)
 * @see Cloud\TceCloud\ClientManager
 */
class TceCloud extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Factory::class;
    }
}
