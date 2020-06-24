<?php

namespace Prozorov\DataVerification\Tests;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Prozorov\DataVerification\Transport\DebugTransport;
use Prozorov\DataVerification\Messages\SmsMessage;
use Prozorov\DataVerification\Models\Code;
use Prozorov\DataVerification\Types\Phone;
use Mockery;

class DebugTransportTest extends MockeryTestCase
{
    public function testMessageIsDelivered()
    {
        $message = $this->getMessage();

        $transport = Mockery::mock(DebugTransport::class, ['/tmp'])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $transport->shouldReceive('getTimestamp')
            ->andReturn('123');

        $transport->shouldReceive('putContents')
            ->andReturn(20);

        $transport->send($message);
    }

    public function testExceptionIsThrown()
    {
        $message = $this->getMessage();

        $transport = Mockery::mock(DebugTransport::class, ['/tmp'])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $transport->shouldReceive('getTimestamp')
            ->andReturn('123');

        $transport->shouldReceive('putContents')
            ->andReturn(0);

        $this->expectException(\RuntimeException::class);

        $transport->send($message);
    }

    /**
     * getMessage.
     *
     * @access	protected
     * @return	SmsMessage
     */
    protected function getMessage(): SmsMessage
    {
        $code = new Code(['PASS' => 1234]);
        $phone = new Phone('89181234567');

        $message = new SmsMessage();
        $message->setCode($code);
        $message->setAddress($phone);

        return $message;
    }
}
