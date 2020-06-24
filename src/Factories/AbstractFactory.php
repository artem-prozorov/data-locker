<?php

namespace Prozorov\DataVerification\Factories;

use Prozorov\DataVerification\Exceptions\{ConfigurationException, FactoryException};
use Webmozart\Assert\Assert;

abstract class AbstractFactory
{
    /**
     * @var string $allowedType
     */
    protected $allowedType;

    /**
     * @var array $config
     */
    protected $config = [];

    public function __construct(array $config)
    {
        $this->loadConfig($config);
    }

    /**
     * loadConfig.
     *
     * @access	protected
     * @param	array	$config	
     * @return	void
     */
    protected function loadConfig(array $config): void
    {
        foreach ($config as $code => $class) {
            if (!class_exists($class)) {
                throw new FactoryException('Фабрика не сможет создать такой объект: '.$class);
            }
        }

        $this->config = $config;
    }

    /**
     * make.
     *
     * @access	public
     * @param	string	$code	
     * @return	mixed
     */
    public function make(string $code)
    {
        if (empty($this->config)) {
            throw new ConfigurationException('Фабрика не инициализирована');
        }

        if (! $this->entityExists($code)) {
            throw new FactoryException('Фабрика не может сделать такую сущность');
        }

        $resolved = new $this->config[$code];

        Assert::isInstanceOf($resolved, $this->allowedType);

        return $resolved;
    }

    /**
     * entityExists.
     *
     * @access	public
     * @param	string	$code	
     * @return	bool
     */
    public function entityExists(string $code): bool
    {
        return array_key_exists($code, $this->config);
    }
}
