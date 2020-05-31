<?php

declare(strict_types=1);

namespace Prozorov\DataVerification\Traits;

trait Singleton
{
    /**
     * @var Singleton
     */
    protected static $instance;

    /**
     * gets the instance via lazy initialization (created on first usage)
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * is not allowed to call from outside to prevent from creating multiple instances,
     * to use the singleton, you have to obtain the instance from Singleton::getInstance() instead
     */
    protected function __construct()
    {
    }

    /**
     * prevent the instance from being cloned (which would create a second instance of it)
     */
    protected function __clone()
    {
    }

    /**
     * prevent from being unserialized (which would create a second instance of it)
     */
    protected function __wakeup()
    {
    }
}
