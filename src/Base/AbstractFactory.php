<?php

namespace Prozorov\DataVerification\Base;

use Prozorov\DataVerification\Exceptions\{ConfigurationException, FactoryException};

abstract class AbstractFactory
{
    protected $config = [];

    public function loadConfig(array $config)
    {
        foreach ($config as $code => $class) {
            if (!class_exists($class)) {
                throw new FactoryException('Фабрика не сможет создать такой объект: '.$class);
            }
        }

        $this->config = $config;
    }

    public function make(string $code)
    {
        if (empty($this->config)) {
            throw new ConfigurationException('Фабрика не инициализирована');
        }

        if (! $this->entityExists($code)) {
            throw new FactoryException('Фабрика не может сделать такую сущность');
        }

        return new $this->config[$code];
    }

    public function entityExists(string $code): bool
    {
        return array_key_exists($code, $this->config);
    }

    public function resetConfig()
    {
        $this->config = [];
    }
}
