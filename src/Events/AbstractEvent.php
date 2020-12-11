<?php

declare(strict_types=1);

namespace Prozorov\DataVerification\Events;

abstract class AbstractEvent
{
    /**
     * @var string $name
     */
    protected $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Get $name
     *
     * @return  string
     */ 
    public function getName()
    {
        return $this->name;
    }
}
