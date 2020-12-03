<?php

declare(strict_types=1);

namespace Prozorov\DataVerification;

use Prozorov\DataVerification\Contracts\CodeRepositoryInterface;
use Prozorov\DataVerification\Factories\{TransportFactory, MessageFactory};
use Prozorov\DataVerification\Messages\SmsMessage;
use Prozorov\DataVerification\Transport\DebugTransport;
use Psr\EventDispatcher\EventDispatcherInterface;
use Prozorov\DataVerification\Events\AbstractEvent;
use Psr\Container\ContainerInterface;

class Configuration
{
    /**
     * @var ContainerInterface $container
     */
    protected $container;

    /**
     * @var EventDispatcherInterface $eventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @var CodeRepositoryInterface $codeRepo
     */
    protected $codeRepo;

    /**
     * @var MessageFactory $messageFactory
     */
    protected $messageFactory;

    /**
     * @var TransportFactory $transportFactory
     */
    protected $transportFactory;

    /**
     * @var array $resolve
     */
    protected $resolved = [];

    /**
     * @var array $passwords
     */
    protected $passwords = [];

    /**
     * loadConfig.
     *
     * @access	public
     * @param	array	$config	Default: []
     * @return	void
     */
    public function __construct(
        ContainerInterface $container = null,
        array $config = [],
        EventDispatcherInterface $eventDispatcher = null
    )
    {
        $this->container = $container;
        $this->eventDispatcher = $eventDispatcher;

        if (empty($config['code_repository'])) {
            throw new \InvalidArgumentException('Укажите класс-репозиторий данных');
        }

        $this->codeRepo = $config['code_repository'];

        $transportConfig = $config['transport'] ?? ['sms' => DebugTransport::class];
        $this->transportFactory = new TransportFactory($transportConfig);

        $messageConfig = $config['messages'] ?? ['sms' => SmsMessage::class];
        $this->messageFactory = new MessageFactory($messageConfig);

        if (! empty($config['passwords'])) {
            foreach ($config['passwords'] as $code => $passwordConfig) {
                $this->passwords[$code] = $this->getPreparedPasswordConfig($passwordConfig);
            }
        } else {
            $this->passwords['default'] = [
                'allowed_symbols' => $config['allowed_symbols'] ?? range(0, 9),
                'pass_length' => $config['pass_length'] ?? 4,
                'creation_code_threshold' => $config['creation_code_threshold'] ?? 60,
                'limit_per_hour' => $config['limit_per_hour'] ?? 60,
                'attempts' => $config['attempts'] ?? 3,
                'password_validation_period' => $config['password_validation_period'] ?? 3600,
            ];
        }
    }

    /**
     * Get the value of codeRepo
     *
     * @access	public
     * @return	CodeRepositoryInterface
     */
    public function getCodeRepo(): CodeRepositoryInterface
    {
        if (is_object($this->codeRepo) && ($this->codeRepo instanceof CodeRepositoryInterface)) {
            return $this->codeRepo;
        }

        return $this->getResolved('code_repository', $this->codeRepo);
    }

    /**
     * getAllowedSymbols.
     *
     * @access	public
     * @param	string	$code	Default: 'default'
     * @return	array
     */
    public function getAllowedSymbols(string $code = 'default'): array
    {
        return $this->passwords[$code]['allowed_symbols'];
    }

    /**
     * getPassLength.
     *
     * @access	public
     * @param	string	$code	Default: 'default'
     * @return	int
     */
    public function getPassLength(string $code = 'default'): int
    {
        return $this->passwords[$code]['pass_length'];
    }

    /**
     * Returns seconds threshold
     * 
     * @access	public
     * @param	string	$code	Default: 'default'
     * @return	integer
     */
    public function getCreationCodeThreshold(string $code = 'default'): int
    {
        return $this->passwords[$code]['creation_code_threshold'];
    }

    /**
     * getLimitPerHour.
     *
     * @access	public
     * @param	string	$code	Default: 'default'
     * @return	int
     */
    public function getLimitPerHour(string $code = 'default'): int
    {
        return $this->passwords[$code]['limit_per_hour'];
    }

    /**
     * getAttempts.
     *
     * @access	public
     * @param	string	$code	Default: 'default'
     * @return	int
     */
    public function getAttempts(string $code = 'default'): int
    {
        return $this->passwords[$code]['attempts'];
    }

    /**
     * Returns seconds threshold
     * 
     * @access	public
     * @param	string	$code	Default: 'default'
     * @return	integer
     */
    public function getPasswordValidationPeriod(string $code = 'default'): int
    {
        return $this->passwords[$code]['password_validation_period'];
    }

    /**
     * getTransportFactory.
     *
     * @access	public
     * @return	TransportFactory
     */
    public function getTransportFactory(): TransportFactory
    {
        return $this->transportFactory;
    }

    /**
     * getMessageFactory.
     *
     * @access	public
     * @return	MessageFactory
     */
    public function getMessageFactory(): MessageFactory
    {
        return $this->messageFactory;
    }

    /**
     * emitEvent.
     *
     * @access	public
     * @param	AbstractEvent	$event	
     * @return	void
     */
    public function emitEvent(AbstractEvent $event): void
    {
        if (empty($this->eventDispatcher)) {
            return;
        }

        $this->eventDispatcher->dispatch($event);
    }

    /**
     * getResolved.
     *
     * @access	protected
     * @param	string	$definition    	
     * @param	mixed	$implementation	
     * @return	mixed
     */
    protected function getResolved(string $definition, $implementation)
    {
        if (empty($this->resolved[$definition])) {
            $this->resolve($definition, $implementation);
        }

        return $this->resolved[$definition];
    }

    /**
     * resolve.
     *
     * @access	protected
     * @param	string	$definition    	
     * @param	mixed	$implementation	
     * @return	void
     */
    protected function resolve(string $definition, $implementation): void
    {
        if (is_callable($implementation)) {
            $this->resolved[$definition] = $implementation();

            return;
        }

        if (is_object($implementation)) {
            $this->resolved[$definition] = $implementation;

            return;
        }

        $this->resolved[$definition] = $this->container->get($implementation);
    }

    /**
     * getPreparedPasswordConfig.
     *
     * @access	protected
     * @param	array	$passwordConfig	
     * @return	array
     */
    protected function getPreparedPasswordConfig(array $passwordConfig): array
    {
        $default = [
            'pass_length' => 4,
            'creation_code_threshold' => 60,
            'limit_per_hour' => 10,
            'attempts' => 3,
            'password_validation_period' => 3600,
        ];

        return array_merge($default, $passwordConfig);
    }
}
