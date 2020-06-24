<?php

declare(strict_types=1);

namespace Prozorov\DataVerification\Models;

use Prozorov\DataVerification\Contracts\VerificationDataInterface;
use Prozorov\DataVerification\App\Configuration;
use Prozorov\DataVerification\Types\Address;
use Datetime;

class Code
{
    /**
     * @var integer $id
     */
    protected $id = 0;

    /**
     * @var string|null $verificationCode
     */
    protected $verificationCode;

    /**
     * @var string|null $pass
     */
    protected $pass;

    /**
     * @var Address $address
     */
    protected $address;

    /**
     * @var array $verificationData
     */
    protected $verificationData = [];

    /**
     * @var integer $attempts
     */
    protected $attempts = 0;

    /**
     * @var bool $validated
     */
    protected $validated = false;

    /**
     * @var DateTime $createdAt
     */
    protected $createdAt;

    public function __construct(array $data = [])
    {
        if (empty($data)) {
            return;
        }

        $this->id = $data['ID'] ?? 0;
        $this->createdAt = $data['CREATED_AT'] ?? new DateTime();
        $this->verificationCode = (string) ($data['VERIFICATION_CODE'] ?? null);
        $this->pass = (string) ($data['PASS'] ?? null);
        $this->attempts = $data['ATTEMPTS'] ?? 0;
        $this->validated = ($data['VALIDATED'] === 'Y') ? true : false;
        $this->verificationData = $data['DATA'] ?? [];

        $address = (string) ($data['ADDRESS'] ?? '');
        $this->address = new Address($address);
    }

    /**
     * Get the value of id
     *
     * @access	public
     * @return	int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @access	public
     * @param	int	$id	
     * @return	Code
     */
    public function setId(int $id): Code
    {
        $this->id = $id;

        return $this;
    }

    /**
     * getCreatedAt.
     *
     * @access	public
     * @return	Datetime
     */
    public function getCreatedAt(): Datetime
    {
        if (empty($this->createdAt)) {
            $this->createdAt = new Datetime();
        }

        return $this->createdAt;
    }

    /**
     * setCreatedAt.
     *
     * @access	public
     * @param	Datetime	$datetime	
     * @return	Code
     */
    public function setCreatedAt(Datetime $datetime): Code
    {
        $this->createdAt = $datetime;

        return $this;
    }

    /**
     * isNew.
     *
     * @access	public
     * @return	bool
     */
    public function isNew(): bool
    {
        return $this->id <= 0;
    }

    /**
     * getVerificationCode.
     *
     * @access	public
     * @return	string
     */
    public function getVerificationCode(): string
    {
        return $this->verificationCode;
    }

    /**
     * setVerificationCode.
     *
     * @access	public
     * @param	string	$code	
     * @return	Code
     */
    public function setVerificationCode(string $code): Code
    {
        $this->verificationCode = $code;

        return $this;
    }

    /**
     * getOneTimePass.
     *
     * @access	public
     * @return	string
     */
    public function getOneTimePass(): string
    {
        return $this->pass;
    }

    /**
     * setOneTimePass.
     *
     * @access	public
     * @param	string	$pass	
     * @return	Code
     */
    public function setOneTimePass(string $pass): Code
    {
        $this->pass = $pass;

        return $this;
    }

    /**
     * getAddress.
     *
     * @access	public
     * @return	Address
     */
    public function getAddress(): Address
    {
        if (empty($this->address)) {
            throw new \InvalidArgumentException('Адрес не установлен');
        }

        return $this->address;
    }

    /**
     * setAddress.
     *
     * @access	public
     * @param	Address	$address	
     * @return	Code
     */
    public function setAddress(Address $address): Code
    {
        $this->address = $address;

        return $this;
    }

    /**
     * getVerificationData.
     *
     * @access	public
     * @return	array
     */
    public function getVerificationData(): array
    {
        return $this->verificationData;
    }

    /**
     * setVerificationData.
     *
     * @access	public
     * @param	array	$data	
     * @return	Code
     */
    public function setVerificationData(array $data): Code
    {
        $this->verificationData = $data;

        return $this;
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
     * setAttempts.
     *
     * @access	public
     * @param	int	$attempts	
     * @return	Code
     */
    public function setAttempts(int $attempts): Code
    {
        $this->attempts = $attempts;

        return $this;
    }

    /**
     * incrementAttempts.
     *
     * @access	public
     * @return	Code
     */
    public function incrementAttempts(): Code
    {
        $this->attempts++;

        return $this;
    }

    /**
     * isValidated.
     *
     * @access	public
     * @return	bool
     */
    public function isValidated(): bool
    {
        return $this->validated;
    }

    /**
     * setValidated.
     * 
     * @access	public
     * @return	Code
     */
    public function setValidated(): Code
    {
        $this->validated = true;

        return $this;
    }
}
