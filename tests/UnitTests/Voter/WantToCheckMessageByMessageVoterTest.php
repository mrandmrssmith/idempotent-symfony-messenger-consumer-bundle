<?php

namespace Tests\UnitTests\Voter;

use MrAndMrsSmith\IdempotentConsumerSymfonyMessengerBundle\Voter\WantToCheckMessageByMessageVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\AbstractWorkerMessageEvent;

class WantToCheckMessageByMessageVoterTest extends TestCase
{
    /** @var AbstractWorkerMessageEvent */
    private $event;

    public function setUp(): void
    {
        $this->event = $this->createMock(AbstractWorkerMessageEvent::class);
        $envelope = new Envelope(new DummyMessageClass());
        $this->event->method('getEnvelope')->willReturn($envelope);
    }

    public function testWillReturnTrueWhenSupportedMessageIsEmpty(): void
    {
        $voter = $this->createVoter();

        $this->assertTrue($voter->vote($this->event));
    }

    public function testWillReturnTrueWhenMessageIsFromSupportedMessage(): void
    {
        $voter = $this->createVoter([DummyMessageClass::class]);

        $this->assertTrue($voter->vote($this->event));
    }

    public function testWillReturnFalseWhenMessageIsFromNotSupportedMessage(): void
    {
        $voter = $this->createVoter(['AnotherMessageClass']);

        $this->assertFalse($voter->vote($this->event));
    }

    /**
     * @param string[] $supportedMessages
     */
    private function createVoter(array $supportedMessages = []): WantToCheckMessageByMessageVoter
    {
        return new WantToCheckMessageByMessageVoter($supportedMessages);
    }
}
