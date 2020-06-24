<?php

declare(strict_types=1);

namespace Prozorov\DataVerification;

use Prozorov\DataVerification\Exceptions\{LimitException, VerificationException};
use Prozorov\DataVerification\Models\Code;
use Prozorov\DataVerification\Types\Address;

class CodeManager
{
    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * generate.
     *
     * @access	public
     * @param	Address	$address	
     * @param	array                      	$data   	Default: null
     * @return	Code
     */
    public function generate(Address $address, array $data = null): Code
    {
        $this->checkCreationLimit($address);

        $verificationCode = md5((string) strtotime('now'));

        $code = new Code();
        $code->setVerificationCode($verificationCode)
            ->setOneTimePass($this->generateOTP())
            ->setAddress($address);

        if (!empty($data)) {
            $code->setVerificationData($data);
        }

        $this->config->getCodeRepo()->save($code);

        return $code;
    }

    /**
     * verify.
     *
     * @access	public
     * @param	string	$verificationCode	
     * @param	string	$pass            	
     * @return	Code
     */
    public function verify(string $verificationCode, string $pass): Code
    {
        $seconds = $this->config->getPasswordValidationPeriod();

        $createdAfter = (new \Datetime())->sub(new \DateInterval('PT'.$seconds.'S'));
        $code = $this->config->getCodeRepo()->getOneUnvalidatedByCode($verificationCode, $createdAfter);

        if (!$code) {
            throw new \OutOfBoundsException('Данные не найдены');
        }

        if ($code->getOneTimePass() !== $pass) {
            $code->incrementAttempts();
            throw new VerificationException('Некорректно указан код');
        }

        if ($this->config->getAttempts() <= $code->getAttempts()) {
            throw new LimitException('Превышен лимит');
        }

        $code->setValidated();
        
        $this->config->getCodeRepo()->save($code);

        return $code;
    }

    /**
     * generateOTP.
     *
     * @access	protected
     * @return	string
     */
    protected function generateOTP(): string
    {
        $symbols = $this->config->getAllowedSymbols();
        $length = $this->config->getPassLength();
        $otp = '';
        for ($i = 1; $i <= $length; $i++) {
            $otp .= $symbols[rand(0, (count($symbols) - 1))];
        }

        return $otp;
    }

    /**
     * checkCreationLimit.
     *
     * @access	protected
     * @param	Address	$address	
     * @return	void
     */
    protected function checkCreationLimit(Address $address): void
    {
        $threshold = $this->config->getCreationCodeThreshold();

        $createdAfter = (new \Datetime())->sub(new \DateInterval('PT'.$threshold.'S'));

        if ($this->config->getCodeRepo()->getLastCodeForAddress($address, $createdAfter)) {
            throw new LimitException('Превышен лимит');
        }

        $createdAfter = (new \Datetime())->sub(new \DateInterval('PT3600S'));
        if ($attempts = $this->config->getCodeRepo()->getCodesCountForAddress($address, $createdAfter)) {
            $limitPerHour = $this->config->getLimitPerHour();
            if ($limitPerHour < $attempts) {
                throw new LimitException('Превышен лимит обращений в час');
            }
        }
    }
}
