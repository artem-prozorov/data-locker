<?php

namespace Prozorov\DataVerification\Factories;

use Prozorov\DataVerification\Exceptions\{ConfigurationException, FactoryException};
use Webmozart\Assert\Assert;
use Psr\Container\ContainerInterface;

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

    /**
     * @var array $resolved
     */
    protected $resolved = [];

    /**
     * @var bool $singletons
     */
    protected $singletons = true;

    /**
     * @var ContainerInterface $container
     */
    protected $container;

    public function __construct(array $config, ContainerInterface $container = null)
    {
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
        if (! empty($this->resolved[$code])) {
            return $this->resolved[$code];
        }

        if (empty($this->config)) {
            throw new ConfigurationException('Фабрика не инициализирована');
        }

        if (empty($this->config[$code])) {
            throw new FactoryException('Отсутствуют инструкции по созданию объекта ' . $code);
        }

        if (is_callable($this->config[$code])) {
            $resolved = $this->config[$code]();
        } elseif (is_string($code)) {
            $resolved = $this->getResolvedFromString($code);
        }

        Assert::isInstanceOf($resolved, $this->allowedType);

        if (! $this->singletons) {
            return $resolved;
        }

        $this->resolved[$code] = $resolved;

        return $this->resolved[$code];
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

    /**
     * getResolvedFromString.
     *
     * @access	protected
     * @return	mixed
     */
    protected function getResolvedFromString(string $code)
    {
        if (! $this->entityExists($code)) {
            throw new FactoryException('Фабрика не может сделать такую сущность');
        }

        if (empty($this->container)) {
            return new $this->config[$code];
        }

        return $this->container->get($this->config[$code]);
    }
}
