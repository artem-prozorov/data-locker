<?php

use PHPUnit\Framework\TestCase;
use Prozorov\DataVerification\Factories\{MessageFactory, TransportFactory};
use Prozorov\DataVerification\Messages\SmsMessage;
use Prozorov\DataVerification\Transport\DebugTransport;
use Prozorov\DataVerification\Exceptions\{ConfigurationException, FactoryException};

class FactoryTest extends TestCase
{
    public function testMessageFactory()
    {
        $factory = new MessageFactory([
            'sms' => SmsMessage::class,
        ]);

        $message = $factory->make('sms');

        $this->assertTrue($message instanceof SmsMessage);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_once_invalid_code_is_passed()
    {
        $this->expectException(FactoryException::class);

        $factory = new MessageFactory([
            'sms' => SmsMessage::class,
        ]);

        $factory->make('test');
    }

    public function testTransportFactory()
    {
        $factory = new TransportFactory([
            'debug' => DebugTransport::class,
        ]);

        $message = $factory->make('debug');

        $this->assertTrue($message instanceof DebugTransport);
    }
}
