<?php

declare(strict_types=1);

namespace Prozorov\DataVerification\Transport;

use Prozorov\DataVerification\Contracts\TransportInterface;
use Prozorov\DataVerification\Messages\AbstractMessage;
use Prozorov\DataVerification\Exceptions\TransportException;

class DebugTransport implements TransportInterface
{
    /**
     * @var string $path
     */
    protected $path;

    public function __construct(string $path = null)
    {
        if (empty($path)) {
            $path = realpath(__DIR__ . '/../../tests/data');
        }

        $this->path = $path;
    }

    /**
     * @inheritDoc
     */
    public function send(AbstractMessage $message): void
    {
        try {
            $text = $message->render();
            $address = $message->getAddress();

            $filename = $this->getPath() . '/' . $address->__toString() . '_' . strtotime('now') . '.txt';

            file_put_contents($filename, $text);
        } catch (\Exception $exception) {
            throw new TransportException('Unable to send message', 1, $exception);
        }
    }

    /**
     * getPath.
     *
     * @access	protected
     * @return	string
     */
    protected function getPath(): string
    {
        return (string) $this->path;
    }
}
