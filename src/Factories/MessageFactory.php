<?php

namespace Prozorov\DataVerification\Factories;

use Prozorov\DataVerification\Messages\AbstractMessage;

class MessageFactory extends AbstractFactory
{
    /**
     * @var string $allowedType
     */
    protected $allowedType = AbstractMessage::class;

    /**
     * @var bool $singletons
     */
    protected $singletons = false;
}
