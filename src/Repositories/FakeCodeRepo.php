<?php

namespace Prozorov\DataVerification\Repositories;

use Prozorov\DataVerification\Contracts\CodeRepositoryInterface;
use Prozorov\DataVerification\Integrations;
use Prozorov\DataVerification\Models\Code;
use Prozorov\DataVerification\Types\Address;
use Datetime;

class FakeCodeRepo implements CodeRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function save(Code $code): Code
    {
        return new Code();
    }

    /**
     * @inheritDoc
     */
    public function delete(Code $code): void
    {
        
    }

    /**
     * @inheritDoc
     */
    public function create(Code $code): Code
    {
        return new Code();
    }

    /**
     * @inheritDoc
     */
    public function update(Code $code): Code
    {
        return $code;
    }

    /**
     * @inheritDoc
     */
    public function getOneUnvalidatedByCode(string $code, Datetime $createdAfter = null): ?Code
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getLastCodeForAddress(Address $address, Datetime $createdAfter = null): ?Code
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getCodesCountForAddress(Address $address, Datetime $createdAfter = null): ?int
    {
        return 0;
    }

    /**
     * @inheritDoc
     */
    public function openTransaction(): void
    {

    }

    /**
     * @inheritDoc
     */
    public function commitTransaction(): void
    {

    }

    /**
     * @inheritDoc
     */
    public function rollbackTransaction(): void
    {

    }
}
