<?php

declare(strict_types=1);

namespace Prozorov\DataVerification\Types;

class Phone extends Address
{
    /**
     * @inheritDoc
     */
    protected function getValidated(string $address): string
    {
        return preg_replace('/[^0-9]/', '', $address);
    }
}
