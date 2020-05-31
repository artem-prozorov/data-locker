<?php

declare(strict_types=1);

namespace Prozorov\DataVerification\App;

use Prozorov\DataVerification\Traits\Singleton;
use Prozorov\DataVerification\Contracts\CodeRepositoryInterface;
use Prozorov\DataVerification\Factories\{TransportFactory, MessageFactory};

class Configuration
{
    use Singleton;

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
     * loadConfig.
     *
     * @access	public
     * @param	array	$config	Default: []
     * @return	void
     */
    public function loadConfig(array $config = []): void
    {
        if (empty($config['code_repository'])) {
            throw new \InvalidArgumentException('Укажите класс-репозиторий данных');
        }

        $this->codeRepo = new $config['code_repository'];

        $defaultTransport = ['sms' => \Prozorov\DataVerification\Transport\DebugTransport::class];
        $transportConfig = $config['transport_config'] ?? $defaultTransport;
        TransportFactory::getInstance()->loadConfig($transportConfig);

        $defaultMessages = ['sms' => \Prozorov\DataVerification\Messages\SmsMessage::class];
        $messageConfig = $config['messages'] ?? $defaultMessages;
        MessageFactory::getInstance()->loadConfig($messageConfig);

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
        if (empty($this->codeRepo)) {
            $this->loadConfig();
        }

        return $this->codeRepo;
    }

    /**
     * Set the value of codeRepo
     *
     * @access	public
     * @param	mixed	$codeRepo	
     * @return	Configuration
     */
    public function setCodeRepo($codeRepo): Configuration
    {
        $this->codeRepo = $codeRepo;

        return $this;
    }

    /**
     * getAllowedSymbols.
     *
     * @access	public
     * @return	array
     */
    public function getAllowedSymbols(): array
    {
        if (empty($this->allowedSymbols)) {
            $this->loadConfig();
        }

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
        return TransportFactory::getInstance();
    }

    /**
     * getMessageFactory.
     *
     * @access	public
     * @return	MessageFactory
     */
    public function getMessageFactory(): MessageFactory
    {
        return MessageFactory::getInstance();
    }
}
