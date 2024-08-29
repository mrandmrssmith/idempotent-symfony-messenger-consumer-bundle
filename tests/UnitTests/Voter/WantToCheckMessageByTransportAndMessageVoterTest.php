<?php

namespace Tests\UnitTests\Voter;

use MrAndMrsSmith\IdempotentConsumerSymfonyMessengerBundle\Voter\WantToCheckMessageByMessageVoter;
use MrAndMrsSmith\IdempotentConsumerSymfonyMessengerBundle\Voter\WantToCheckMessageByTransportAndMessageVoter;
use MrAndMrsSmith\IdempotentConsumerSymfonyMessengerBundle\Voter\WantToCheckMessageByTransportVoter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Event\AbstractWorkerMessageEvent;

class WantToCheckMessageByTransportAndMessageVoterTest extends TestCase
{
    /** @var AbstractWorkerMessageEvent|MockObject */
    private $event;

    /** @var WantToCheckMessageByTransportVoter|MockObject */
    private $transportVoter;

    /** @var WantToCheckMessageByMessageVoter|MockObject */
    private $messageVoter;

    /** @var WantToCheckMessageByTransportAndMessageVoter */
    private $voter;

    public function setUp(): void
    {
        $this->event = $this->createMock(AbstractWorkerMessageEvent::class);
        $this->transportVoter = $this->createMock(WantToCheckMessageByTransportVoter::class);
        $this->messageVoter = $this->createMock(WantToCheckMessageByMessageVoter::class);

        $this->voter = new WantToCheckMessageByTransportAndMessageVoter($this->transportVoter, $this->messageVoter);
    }

    public function testWillReturnTrueWhenBothTrue(): void
    {
        $this->transportVoter->method('vote')->willReturn(true);
        $this->messageVoter->method('vote')->willReturn(true);

        $this->assertTrue($this->voter->vote($this->event));
    }

    public function testWillReturnTrueWhenMessageReturnFalseAndTransportReturnTrue(): void
    {
        $this->transportVoter->method('vote')->willReturn(true);
        $this->messageVoter->method('vote')->willReturn(false);

        $this->assertTrue($this->voter->vote($this->event));
    }

    public function testWillReturnTrueWhenMessageReturnTrueAndTransportReturnFalse(): void
    {
        $this->transportVoter->method('vote')->willReturn(false);
        $this->messageVoter->method('vote')->willReturn(true);

        $this->assertTrue($this->voter->vote($this->event));
    }

    public function testWillReturnFalseWhenBothReturnFalse(): void
    {
        $this->transportVoter->method('vote')->willReturn(false);
        $this->messageVoter->method('vote')->willReturn(false);

        $this->assertFalse($this->voter->vote($this->event));
    }
}
