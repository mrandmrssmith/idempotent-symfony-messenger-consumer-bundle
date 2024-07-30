<?php

namespace MrAndMrsSmith\IdempotentConsumerSymfonyMessengerBundle\EventSubscriber;

use MrAndMrsSmith\IdempotentConsumerBundle\Checker\CheckMessageCanBeProcessed;
use MrAndMrsSmith\IdempotentConsumerBundle\Finalizer\MessageFinalizer;
use MrAndMrsSmith\IdempotentConsumerBundle\Message\IncomingMessage;
use MrAndMrsSmith\IdempotentConsumerSymfonyMessengerBundle\Factory\IncomingMessageFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;

class SymfonyMessengerEventSubscriber implements EventSubscriberInterface
{
    private $checker;

    private $incomingMessageFactory;

    private $finalizer;

    public function __construct(
        CheckMessageCanBeProcessed $checker,
        IncomingMessageFactory $incomingMessageFactory,
        MessageFinalizer $finalizer
    ) {
        $this->checker = $checker;
        $this->incomingMessageFactory = $incomingMessageFactory;
        $this->finalizer = $finalizer;
    }

    public function checkIfCanProcessMessage(WorkerMessageReceivedEvent $event): void
    {
        $incomingMessage = $this->getIncomingMessageFromEnvelope($event->getEnvelope());

        $event->shouldHandle($this->checker->check($incomingMessage));
    }

    public function handleMessageHandledEvent(WorkerMessageHandledEvent $event): void
    {
        $incomingMessage = $this->getIncomingMessageFromEnvelope($event->getEnvelope());

        $this->finalizer->finalizeSuccess($incomingMessage);
    }

    public function handleMessageFailedEvent(WorkerMessageFailedEvent $event): void
    {
        if ($event->willRetry()) {
            return;
        }
        $incomingMessage = $this->getIncomingMessageFromEnvelope($event->getEnvelope());

        $this->finalizer->finalizeFailure($incomingMessage);
    }

    public static function getSubscribedEvents()
    {
        return [
            WorkerMessageReceivedEvent::class => [
                'checkIfCanProcessMessage',
                100
            ],
            WorkerMessageHandledEvent::class => [
                'handleMessageHandledEvent',
                100
            ],
            WorkerMessageFailedEvent::class => [
                'handleMessageFailedEvent',
                100
            ]
        ];
    }

    private function getIncomingMessageFromEnvelope(Envelope $envelope): IncomingMessage
    {
        return $this
            ->incomingMessageFactory
            ->createFromMessengerMessageEnvelope($envelope);
    }
}
