<?php

declare(strict_types=1);

namespace Prozorov\DataVerification;

use Prozorov\DataVerification\Contracts\CodeRepositoryInterface;
use Prozorov\DataVerification\Factories\{TransportFactory, MessageFactory};
use Prozorov\DataVerification\Messages\SmsMessage;
use Prozorov\DataVerification\Transport\DebugTransport;

class Configuration
{
    protected $container;

    /**
     * @var CodeRepositoryInterface $codeRepo
     */
    protected $codeRepo;

    /**
     * @var array $allowedSymbols
     */
    protected $allowedSymbols;

    /**
     * @var integer $passLength
     */
    protected $passLength = 4;

    /**
     * @var integer $creationCodeThreshold
     */
    protected $creationCodeThreshold = 60;

    /**
     * @var integer $limitPerHour
     */
    protected $limitPerHour = 10;

    /**
     * @var integer $attempts
     */
    protected $attempts = 3;

    /**
     * @var integer $passwordValidationPeriod
     */
    protected $passwordValidationPeriod = 3600;

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
     * loadConfig.
     *
     * @access	public
     * @param	array	$config	Default: []
     * @return	void
     */
    public function __construct($container, array $config = [])
    {
        $this->container = $container;

        if (empty($config['code_repository'])) {
            throw new \InvalidArgumentException('Укажите класс-репозиторий данных');
        }

        $this->codeRepo = $config['code_repository'];

        $transportConfig = $config['transport_config'] ?? ['sms' => DebugTransport::class];
        $this->transportFactory = new TransportFactory($transportConfig);

        $messageConfig = $config['messages'] ?? ['sms' => SmsMessage::class];
        $this->messageFactory = new MessageFactory($messageConfig);

        $this->allowedSymbols = $config['allowed_symbols'] ?? range(0, 9);
        $this->passLength = $config['pass_length'] ?? 4;
        $this->creationCodeThreshold = $config['creation_code_threshold'] ?? 60;
        $this->limitPerHour = $config['limit_per_hour'] ?? 60;
        $this->attempts = $config['attempts'] ?? 3;
        $this->passwordValidationPeriod = $config['password_validation_period'] ?? 3600;
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
     * @return	array
     */
    public function getAllowedSymbols(): array
    {
        return $this->allowedSymbols;
    }

    /**
     * getPassLength.
     *
     * @access	public
     * @return	int
     */
    public function getPassLength(): int
    {
        return $this->passLength;
    }

    /**
     * Returns seconds threshold
     * 
     * @access	public
     * @return	integer
     */
    public function getCreationCodeThreshold(): int
    {
        return $this->creationCodeThreshold;
    }

    /**
     * getLimitPerHour.
     *
     * @access	public
     * @return	int
     */
    public function getLimitPerHour(): int
    {
        return $this->limitPerHour;
    }

    /**
     * getAttempts.
     *
     * @access	public
     * @return	int
     */
    public function getAttempts(): int
    {
        return $this->attempts;
    }

    /**
     * Returns seconds threshold
     * 
     * @access	public
     * @return	integer
     */
    public function getPasswordValidationPeriod(): int
    {
        return $this->passwordValidationPeriod;
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
}
