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

    /**
     * @var array $resolved
     */
    protected $resolved = [];

    public function __construct(array $config)
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

        $this->resolved[$code] = $resolved;

        return $this->resolved[$code];
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

        return new $this->config[$code];
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
