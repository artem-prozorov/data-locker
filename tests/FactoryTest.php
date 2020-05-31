<?php

use PHPUnit\Framework\TestCase;
use Prozorov\DataVerification\Factories\{MessageFactory, TransportFactory};
use Prozorov\DataVerification\Messages\SmsMessage;
use Prozorov\DataVerification\Transport\DebugTransport;
use Prozorov\DataVerification\Exceptions\{ConfigurationException, FactoryException};

class FactoryTest extends TestCase
{
    public function tearDown(): void
    {
        parent::tearDown();

        MessageFactory::getInstance()->resetConfig();
        TransportFactory::getInstance()->resetConfig();
    }

    public function testMessageFactory()
    {
        $factory = MessageFactory::getInstance();

        $factory->loadConfig([
            'sms' => SmsMessage::class,
        ]);

        $message = $factory->make('sms');

        $this->assertTrue($message instanceof SmsMessage);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_no_config_is_loaded()
    {
        $this->expectException(ConfigurationException::class);

        MessageFactory::getInstance()->make('test');
    }

    /**
     * @test
     */
    public function it_throws_an_exception_once_invalid_code_is_passed()
    {
        $this->expectException(FactoryException::class);

        MessageFactory::getInstance()->loadConfig([
            'sms' => SmsMessage::class,
        ]);

        MessageFactory::getInstance()->make('test');
    }

    public function testTransportFactory()
    {
        $factory = TransportFactory::getInstance();

        $factory->loadConfig([
            'debug' => DebugTransport::class,
        ]);

        $message = $factory->make('debug');

        $this->assertTrue($message instanceof DebugTransport);
    }
}
