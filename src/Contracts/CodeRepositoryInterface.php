<?php

declare(strict_types=1);

namespace Prozorov\DataVerification\Contracts;

use Prozorov\DataVerification\Models\Code;
use Prozorov\DataVerification\Types\Address;
use Datetime;

interface CodeRepositoryInterface
{
    /**
     * save.
     *
     * @access	public
     * @param	Code	$code	
     * @return	Code
     */
    public function save(Code $code): Code;

    /**
     * delete.
     *
     * @access	public
     * @param	Code	$code	
     * @return	void
     */
    public function delete(Code $code): void;

    /**
     * getOneUnvalidatedByCode.
     *
     * @access	public
     * @param	string  	$code        	
     * @param	Datetime	$createdAfter	Default: null
     * @return	Code|void
     */
    public function getOneUnvalidatedByCode(string $code, Datetime $createdAfter = null): ?Code;

    /**
     * getLastCodeForAddress.
     *
     * @access	public
     * @param	Address	$address     	
     * @param	Datetime                   	$createdAfter	Default: null
     * @return	Code|void
     */
    public function getLastCodeForAddress(Address $address, Datetime $createdAfter = null): ?Code;

    /**
     * getCodesCountForAddress.
     *
     * @access	public
     * @param	Address	$address     	
     * @param	Datetime                   	$createdAfter	Default: null
     * @return	int|null
     */
    public function getCodesCountForAddress(Address $address, Datetime $createdAfter = null): ?int;
}
