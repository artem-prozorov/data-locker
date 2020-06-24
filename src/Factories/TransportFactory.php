<?php

namespace Prozorov\DataVerification\Factories;

use Prozorov\DataVerification\Contracts\TransportInterface;

class TransportFactory extends AbstractFactory
{
    /**
     * @var string $allowedType
     */
    protected $allowedType = TransportInterface::class;
}
