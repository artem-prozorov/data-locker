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

            $filename = $this->getPath() . '/' . $address->__toString() . '_' . $this->getTimestamp() . '.txt';

            if (! $this->putContents($filename, $text)) {
                throw new \RuntimeException('Unable to write data');
            }
        } catch (\Exception $exception) {
            throw new TransportException('Unable to send message', 1, $exception);
        }
    }

    /**
     * putContents wrapper
     *
     * @access	protected
     * @param	string	$filename	
     * @param	mixed 	$content 	
     * @return	int|bool
     */
    protected function putContents(string $filename, $content)
    {
        return file_put_contents($filename, $content);
    }

    /**
     * getTimestamp.
     *
     * @access	protected
     * @return	string
     */
    protected function getTimestamp(): string
    {
        return (string) strtotime('now');
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
