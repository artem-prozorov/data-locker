<?php

namespace Prozorov\DataVerification\Contracts;

use Prozorov\DataVerification\Types\Address;

interface TransportInterface
{
    public function send(Address $address, string $text);
}
