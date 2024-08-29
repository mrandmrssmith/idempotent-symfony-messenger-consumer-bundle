<?php

namespace Tests\UnitTests\Voter;

use MrAndMrsSmith\IdempotentConsumerSymfonyMessengerBundle\Voter\WantToCheckMessageByTransportVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Event\AbstractWorkerMessageEvent;

class WantToCheckMessageByTransportVoterTest extends TestCase
{
    private const MESSAGE_TRANSPORT = 'message_transport';

    /** @var AbstractWorkerMessageEvent */
    private $event;

    public function setUp(): void
    {
        $this->event = $this->createMock(AbstractWorkerMessageEvent::class);
        $this->event->method('getReceiverName')->willReturn(self::MESSAGE_TRANSPORT);
    }

    public function testWillReturnTrueWhenSupportedTransportIsEmpty(): void
    {
        $voter = $this->createVoter();

        $this->assertTrue($voter->vote($this->event));
    }

    public function testWillReturnTrueWhenMessageIsFromSupportedTransport(): void
    {
        $voter = $this->createVoter([self::MESSAGE_TRANSPORT]);

        $this->assertTrue($voter->vote($this->event));
    }

    public function testWillReturnFalseWhenMessageIsFromNotSupportedTransport(): void
    {
        $voter = $this->createVoter(['another_transport']);

        $this->assertFalse($voter->vote($this->event));
    }

    /**
     * @param string[] $supportedTransports
     */
    private function createVoter(array $supportedTransports = []): WantToCheckMessageByTransportVoter
    {
        return new WantToCheckMessageByTransportVoter($supportedTransports);
    }
}
