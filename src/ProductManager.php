<?php

namespace Cloud\TceCloud;

use Illuminate\Support\Manager;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Cloud\TceCloud\Drivers\CommonProvider;
use Cloud\TceCloud\Drivers\OpbillProvider;
use Cloud\TceCloud\Contracts\Factory;


/**
 * Tce product manager
 * Determine which product'sdk to use
 */
class ProductManager extends Manager implements Factory
{
    /**
     * Get a driver instance.
     *
     * @param  string  $driver
     * @return mixed
     */
    public function with($driver)
    {
        return $this->driver($driver);
    }

    /**
     * Create a new driver instance.
     *
     * @param  string  $driver
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    protected function createDriver($driver)
    {
        // First, we will determine if a custom driver creator exists for the given driver and
        // if it does not we will check for a creator method for the driver. Custom creator
        // callbacks allow developers to build their own "drivers" easily using Closures.
        if (isset($this->customCreators[$driver])) {
            return $this->callCustomCreator($driver);
        } else {
            $method = 'create'.Str::studly($driver).'Driver';

            if (method_exists($this, $method)) {
                return $this->$method();
            } else { // Use the common driver creator if the specified driver creator not exists
                return $this->createCommonDriver($driver);
            }
        }

        throw new InvalidArgumentException("Driver [$driver] not supported.");
    }

    public function buildProvider($driver, $provider, $config)
    {
        return new $provider(
            $driver,
            collect($config)
        );
    }

    /**
     * Get the default driver name.
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function getDefaultDriver()
    {
        throw new InvalidArgumentException('No Cloud driver was specified.');
    }


    /**
     * Create an instance of the common driver.
     *
     * @return \Cloud\TceCloud\Drivers\CommonProvider
     */
    protected function createCommonDriver($driver)
    {
        $config = $this->config->get('tcecloud');

        return $this->buildProvider(
            $driver, CommonProvider::class, $config
        );
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return Mou
     */
    protected function createOpbillDriver()
    {
        $config = $this->config->get('tcecloud');

        return $this->buildProvider(
            'opbill', OpbillProvider::class, $config
        );
    }
}
