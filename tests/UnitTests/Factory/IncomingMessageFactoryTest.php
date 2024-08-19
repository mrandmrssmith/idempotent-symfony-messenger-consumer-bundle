<?php

namespace Tests\UnitTests\Factory;

use MrAndMrsSmith\IdempotentConsumerBundle\Message\IncomingMessage;
use MrAndMrsSmith\IdempotentConsumerSymfonyMessengerBundle\Factory\IncomingMessageFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Serializer\SerializerInterface;

class IncomingMessageFactoryTest extends TestCase
{
    /** @var SerializerInterface|MockObject */
    private $serializer;

    /** @var IncomingMessageFactory */
    private $factory;

    public function setUp(): void
    {
        $this->serializer = $this->createMock(SerializerInterface::class);

        $this->factory = new IncomingMessageFactory($this->serializer);
    }

    public function testCreateFromMessengerMessageEnvelope(): void
    {
        $message = new \stdClass();
        $message->foo = 'bar';

        $envelope = new Envelope($message);

        $this->serializer->method('serialize')
            ->willReturnMap([
                [$message, 'json', [], '{"foo":"bar"}'],
                [[], 'json', [], '[]'],
            ]);

        $result = $this->factory->createFromMessengerMessageEnvelope($envelope);

        $this->assertEquals(
            new IncomingMessage(['foo' => 'bar'], [], \stdClass::class),
            $result
        );
    }
}
