<?php

namespace Prozorov\DataVerification\Tests;

use Psr\Container\ContainerInterface;

trait HasContainer
{
    /**
     * getContainer.
     *
     * @access	protected
     * @return	ContainerInterface
     */
    protected function getContainer(): ContainerInterface
    {
        return new class implements ContainerInterface {
            protected $bindings;

            public function get($id)
            {
                return $this->bindings[$id];
            }

            public function has($id)
            {
                return empty($this->bindings[$id]);
            }

            public function setBindings(array $bindings)
            {
                $this->bindings = $bindings;
            }
        };
    }
}
