<?php

declare(strict_types=1);

namespace Prozorov\DataVerification\Types;

class Address
{
    /**
     * @var string $address
     */
    protected $address;

    public function __construct(string $address)
    {
        $this->address = $this->getValidated($address);
    }

    /**
     * toString.
     *
     * @access	public
     * @return	string
     */
    public function __toString(): string
    {
        return $this->address;
    }

    /**
     * getValidated.
     *
     * @access	protected
     * @param	string	$address	
     * @return	string
     */
    protected function getValidated(string $address): string
    {
        return $address;
    }
}
