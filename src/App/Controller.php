<?php

declare(strict_types=1);

namespace Prozorov\DataVerification\App;

use Prozorov\DataVerification\Contracts\MessageInterface;
use Prozorov\DataVerification\Models\Code;
use Prozorov\DataVerification\Types\Address;

class Controller
{
    /**
     * @var Configuration $config
     */
    protected $config;

    /**
     * @var CodeManager $manager
     */
    protected $manager;

    public function __construct()
    {
        $this->config = Configuration::getInstance();
        $this->manager = CodeManager::getInstance();
    }

    /**
     * lockData.
     *
     * @access	public
     * @param	array                      	$data         	- the data that must be locked and verified
     * @param	Address	$address      	- address where we will send one-time password to
     * @param	MessageInterface|string   	$message        - message object or string code for the message factory
     * @return	Code
     */
    public function lockData(array $data, Address $address, $message): Code
    {
        $code = $this->manager->generate($address, $data);

        if (is_string($message)) {
            $message = $this->config->getMessageFactory()->make($message);
        }

        $message->setCode($code)->setAddress($address)->send();

        return $code;
    }

    /**
     * Unlocks the data and gets the protected data
     *
     * @access	public
     * @param	string	$verificationCode	
     * @param	string	$pass            	
     * @return	array
     */
    public function getUnlockedData(string $verificationCode, string $pass): array
    {
        $code = $this->manager->verify($verificationCode, $pass);

        return $code->getVerificationData();
    }

    /**
     * Returns validated data
     *
     * @access	public
     * @param	string	$verificationCode	
     * @return	array
     */
    public function getVerifiedData(string $verificationCode): array
    {
        $code = $this->config->getCodeRepo()->getValidatedCode($verificationCode);

        if (empty($code)) {
            throw new \OutOfBoundsException('Данные не найдены');
        }

        return $code->getVerificationData();
    }
}
