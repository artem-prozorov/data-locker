<?php

declare(strict_types=1);

namespace Prozorov\DataVerification;

use Prozorov\DataVerification\Models\Code;
use Prozorov\DataVerification\Types\Address;
use Prozorov\DataVerification\Messages\AbstractMessage;
use Webmozart\Assert\Assert;

class Locker
{
    /**
     * @var Configuration $config
     */
    protected $config;

    /**
     * @var CodeManager $manager
     */
    protected $manager;

    public function __construct(Configuration $config)
    {
        $this->config = $config;
        $this->manager = new CodeManager($config);
    }

    /**
     * lockData.
     *
     * @access	public
     * @param	array $data - the data that must be locked and verified
     * @param	Address	$address - address where we will send one-time password to
     * @param	AbstractMessage|string $message - message object or string code for the message factory
     * @return	Code
     */
    public function lockData(array $data, Address $address, $message): Code
    {
        try {
            $this->config->getCodeRepo()->openTransaction();

            $code = $this->manager->generate($address, $data);

            if (is_string($message)) {
                $message = $this->config->getMessageFactory()->make($message);
            }

            Assert::isInstanceOf($message, AbstractMessage::class);

            $message->setCode($code)->setAddress($address);

            $transport = $this->config->getTransportFactory()
                ->make($message->getTransportCode());

            $transport->send($message);

            $this->config->getCodeRepo()->commitTransaction();

            return $code;
        } catch (\Exception $exception) {
            $this->config->getCodeRepo()->rollbackTransaction();

            throw $exception;
        }
    }

    /**
     * Generate Code and send
     *
     * @access	public
     * @param	Address	$address	
     * @param	AbstractMessage|string  	$message	
     * @return	Code
     */
    public function generateAndSend(Address $address, $message): Code
    {
        return $this->lockData([], $address, $message);
    }

    /**
     * Verifies 
     *
     * @access	public
     * @param	string	$verificationCode	
     * @param	string	$pass            	
     * @return	array
     */
    public function verifyOrFail(string $verificationCode, string $pass): void
    {
        $this->manager->verify($verificationCode, $pass);
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
}
