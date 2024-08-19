<?php

namespace Tests\UnitTests\EventSubscriber;

use MrAndMrsSmith\IdempotentConsumerBundle\Checker\CheckMessageCanBeProcessed;
use MrAndMrsSmith\IdempotentConsumerBundle\Finalizer\MessageFinalizer;
use MrAndMrsSmith\IdempotentConsumerBundle\Message\IncomingMessage;
use MrAndMrsSmith\IdempotentConsumerSymfonyMessengerBundle\EventSubscriber\SymfonyMessengerEventSubscriber;
use MrAndMrsSmith\IdempotentConsumerSymfonyMessengerBundle\Factory\IncomingMessageFactory;
use MrAndMrsSmith\IdempotentConsumerSymfonyMessengerBundle\Voter\WantToCheckMessageVoter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;

class SymfonyMessengerEventSubscriberTest extends TestCase
{
    /** @var CheckMessageCanBeProcessed|MockObject */
    private $checker;

    /** @var IncomingMessageFactory|MockObject */
    private $incomingMessageFactory;

    /** @var MessageFinalizer|MockObject */
    private $finalizer;

    /** @var ReceiverInterface[]|MockObject[] */
    private $receivers;

    /** @var WantToCheckMessageVoter|MockObject */
    private $wantToCheckMessageVoter;

    /** @var ReceiverInterface|MockObject */
    private $receiver;

    /** @var SymfonyMessengerEventSubscriber */
    private $subscriber;

    public function setUp(): void
    {
        $this->checker = $this->createMock(CheckMessageCanBeProcessed::class);
        $this->incomingMessageFactory = $this->createMock(IncomingMessageFactory::class);
        $this->finalizer = $this->createMock(MessageFinalizer::class);
        $this->receiver = $this->createMock(ReceiverInterface::class);
        $this->receivers = ['receiver' => $this->receiver];
        $this->wantToCheckMessageVoter = $this->createMock(WantToCheckMessageVoter::class);

        $this->subscriber = new SymfonyMessengerEventSubscriber(
            $this->checker,
            $this->incomingMessageFactory,
            $this->finalizer,
            $this->receivers,
            $this->wantToCheckMessageVoter
        );
    }

    public function testCheckIfCanProcessMessageWillIgnoreMessageIfVoterReturnFalse(): void
    {
        $event = $this->createWorkerMessageReceivedEvent();

        $this->wantToCheckMessageVoter
            ->expects($this->once())
            ->method('vote')
            ->with($event)
            ->willReturn(false);
        $this->incomingMessageFactory
            ->expects($this->never())
            ->method('createFromMessengerMessageEnvelope');

        $this->subscriber->checkIfCanProcessMessage($event);
    }

    public function testCheckIfCanProcessMessageWillSkipMessageIfCheckerReturnsFalse(): void
    {
        $event = $this->createWorkerMessageReceivedEvent();
        $incomingMessage = $this->createIncomingMessage();


        $this->wantToCheckMessageVoter
            ->expects($this->once())
            ->method('vote')
            ->with($event)
            ->willReturn(true);
        $this->incomingMessageFactory
            ->expects($this->once())
            ->method('createFromMessengerMessageEnvelope')
            ->with($event->getEnvelope())
            ->willReturn($incomingMessage);
        $this->checker
            ->expects($this->once())
            ->method('check')
            ->with($incomingMessage)
            ->willReturn(false);
        $this->receiver
            ->expects($this->once())
            ->method('ack')
            ->with($event->getEnvelope());

        $this->subscriber->checkIfCanProcessMessage($event);
        $this->assertFalse($event->shouldHandle());
    }

    public function testCheckIfCanProcessMessageWillProcessMessageWhenCheckerReturnsTrue(): void
    {
        $event = $this->createWorkerMessageReceivedEvent();
        $incomingMessage = $this->createIncomingMessage();

        $this->wantToCheckMessageVoter
            ->expects($this->once())
            ->method('vote')
            ->with($event)
            ->willReturn(true);
        $this->incomingMessageFactory
            ->expects($this->once())
            ->method('createFromMessengerMessageEnvelope')
            ->with($event->getEnvelope())
            ->willReturn($incomingMessage);
        $this->checker
            ->expects($this->once())
            ->method('check')
            ->with($incomingMessage)
            ->willReturn(true);
        $this->receiver
            ->expects($this->never())
            ->method('ack');

        $this->subscriber->checkIfCanProcessMessage($event);
        $this->assertTrue($event->shouldHandle());
    }

    public function testHandleMessageHandledEventWillIgnoreWhenVoterReturnsFalse(): void
    {
        $event = $this->createWorkerMessageHandledEvent();

        $this->wantToCheckMessageVoter
            ->expects($this->once())
            ->method('vote')
            ->willReturn(false);
        $this->incomingMessageFactory
            ->expects($this->never())
            ->method('createFromMessengerMessageEnvelope');

        $this->subscriber->handleMessageHandledEvent($event);
    }

    public function testHandleMessageHandledEventWillFinalizeSuccessWhenMessageHandled(): void
    {
        $event = $this->createWorkerMessageHandledEvent();
        $incomingMessage = $this->createIncomingMessage();

        $this->wantToCheckMessageVoter
            ->expects($this->once())
            ->method('vote')
            ->willReturn(true);
        $this->incomingMessageFactory
            ->expects($this->once())
            ->method('createFromMessengerMessageEnvelope')
            ->with($event->getEnvelope())
            ->willReturn($incomingMessage);
        $this->finalizer
            ->expects($this->once())
            ->method('finalizeSuccess')
            ->with($incomingMessage);

        $this->subscriber->handleMessageHandledEvent($event);
    }

    public function testHandleMessageFailedEventWillIgnoreWhenVoterReturnsFalse(): void
    {
        $event = $this->createWorkerMessageFailedEvent();

        $this->wantToCheckMessageVoter
            ->expects($this->once())
            ->method('vote')
            ->willReturn(false);
        $this->incomingMessageFactory
            ->expects($this->never())
            ->method('createFromMessengerMessageEnvelope');

        $this->subscriber->handleMessageFailedEvent($event);
    }

    public function testHandleMessageFailedEventWillMarkAsRetryWhenWillRetry(): void
    {
        $event = $this->createWorkerMessageFailedEvent();
        $incomingMessage = $this->createIncomingMessage();

        $this->wantToCheckMessageVoter
            ->expects($this->once())
            ->method('vote')
            ->willReturn(true);
        $this->incomingMessageFactory
            ->expects($this->once())
            ->method('createFromMessengerMessageEnvelope')
            ->with($event->getEnvelope())
            ->willReturn($incomingMessage);
        $event->setForRetry();
        $this->finalizer
            ->expects($this->once())
            ->method('markAsRetry')
            ->with($incomingMessage);

        $this->subscriber->handleMessageFailedEvent($event);
    }

    public function testHandleMessageFailedEventWillFinalizeFailureWhenWillNotRetry(): void
    {
        $event = $this->createWorkerMessageFailedEvent();
        $incomingMessage = $this->createIncomingMessage();

        $this->wantToCheckMessageVoter
            ->expects($this->once())
            ->method('vote')
            ->willReturn(true);
        $this->incomingMessageFactory
            ->expects($this->once())
            ->method('createFromMessengerMessageEnvelope')
            ->with($event->getEnvelope())
            ->willReturn($incomingMessage);
        $this->finalizer
            ->expects($this->once())
            ->method('finalizeFailure')
            ->with($incomingMessage);

        $this->subscriber->handleMessageFailedEvent($event);
    }

    private function createWorkerMessageReceivedEvent(): WorkerMessageReceivedEvent
    {
        return new WorkerMessageReceivedEvent(new Envelope(new \stdClass()), 'receiver');
    }

    private function createIncomingMessage(): IncomingMessage
    {
         return new IncomingMessage(
            ['some' => 'data'],
            [],
            \stdClass::class
        );
    }

    private function createWorkerMessageHandledEvent(): WorkerMessageHandledEvent
    {
        return new WorkerMessageHandledEvent(new Envelope(new \stdClass()), 'receiver');
    }

    private function createWorkerMessageFailedEvent(): WorkerMessageFailedEvent
    {
        return new WorkerMessageFailedEvent(new Envelope(new \stdClass()), 'receiver', new \Exception());
    }
}
