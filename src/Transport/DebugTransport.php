<?php

declare(strict_types=1);

namespace Prozorov\DataVerification\Transport;

use Prozorov\DataVerification\Contracts\TransportInterface;
use Prozorov\DataVerification\Types\Address;

class DebugTransport implements TransportInterface
{
    /**
     * @var string $path
     */
    public static $path;

    /**
     * setDebugPath.
     *
     * @access	public static
     * @param	string	$path
     * @return	void
     */
    public static function setDebugPath(string $path): void
    {
        static::$path = $path;
    }

    /**
     * @inheritDoc
     */
    public function send(Address $address, string $text)
    {
        $filename = $this->getPath().'/'.$address->toString().'_'.strtotime('now').'.txt';

        file_put_contents($filename, $text);
    }

    /**
     * getPath.
     *
     * @access	protected
     * @return	string
     */
    protected function getPath(): string
    {
        if (empty(static::$path)) {
            return realpath(__DIR__.'/../../tests/data');
        }

        return static::$path;
    }
}
