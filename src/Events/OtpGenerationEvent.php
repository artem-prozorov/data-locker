<?php

declare(strict_types=1);

namespace Prozorov\DataVerification\Events;

use Prozorov\DataVerification\Types\Address;

class OtpGenerationEvent extends AbstractEvent
{
    /**
     * @var string $otp
     */
    protected $otp = '';

    /**
     * @var bool $isModified
     */
    protected $isModified = false;

    /**
     * @var Address $address
     */
    protected $address;

    public function __construct(string $name, Address $address)
    {
        parent::__construct($name);

        $this->address = $address;
    }

    /**
     * Get $otp
     *
     * @return  string
     */ 
    public function getOtp(): string
    {
        return $this->otp;
    }

    /**
     * Set $otp
     *
     * @param  string  $otp
     *
     * @return  self
     */ 
    public function setOtp(string $otp): OtpGenerationEvent
    {
        $this->otp = $otp;

        $this->isModified = true;

        return $this;
    }

    /**
     * Get $isModified
     *
     * @return  bool
     */ 
    public function isModified(): bool
    {
        return $this->isModified;
    }

    /**
     * Get $address
     *
     * @return  Address
     */ 
    public function getAddress(): Address
    {
        return $this->address;
    }
}
