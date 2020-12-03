<?php

namespace Prozorov\DataVerification\Tests;

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

    public function testTransportFactoryCreatesSingletons()
    {
        $factory = new TransportFactory([
            'debug' => DebugTransport::class,
        ]);

        $firstMessage = $factory->make('debug');
        $secondMessage = $factory->make('debug');

        $this->assertTrue($firstMessage instanceof DebugTransport);

        $this->assertSame($firstMessage, $secondMessage);
    }

    public function testMessagesAreNotSingletons()
    {
        $factory = new MessageFactory([
            'sms' => SmsMessage::class,
        ]);

        $firstMessage = $factory->make('sms');
        $secondMessage = $factory->make('sms');

        $this->assertNotSame($firstMessage, $secondMessage);
    }
}
