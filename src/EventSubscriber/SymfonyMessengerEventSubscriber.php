<?php

namespace MrAndMrsSmith\IdempotentConsumerSymfonyMessengerBundle\EventSubscriber;

use MrAndMrsSmith\IdempotentConsumerBundle\Checker\CheckMessageCanBeProcessed;
use MrAndMrsSmith\IdempotentConsumerBundle\Finalizer\MessageFinalizer;
use MrAndMrsSmith\IdempotentConsumerBundle\Message\IncomingMessage;
use MrAndMrsSmith\IdempotentConsumerSymfonyMessengerBundle\Factory\IncomingMessageFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\AbstractWorkerMessageEvent;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;

class SymfonyMessengerEventSubscriber implements EventSubscriberInterface
{
    /** @var CheckMessageCanBeProcessed */
    private $checker;

    /** @var IncomingMessageFactory */
    private $incomingMessageFactory;

    /** @var MessageFinalizer */
    private $finalizer;

    /** @var ReceiverInterface[] */
    private $receivers;

    /** @var string[] */
    private $supportedTransports = [];

    /** @var string[] */
    private $supportedMessages = [];

    private $messageBus;

    /**
     * @param string[] $supportedTransports
     * @param string[] $supportedMessages
     */
    public function __construct(
        CheckMessageCanBeProcessed $checker,
        IncomingMessageFactory $incomingMessageFactory,
        MessageFinalizer $finalizer,
        array $receivers,
        array $supportedTransports = [],
        array $supportedMessages = []
    ) {
        $this->checker = $checker;
        $this->incomingMessageFactory = $incomingMessageFactory;
        $this->finalizer = $finalizer;
        $this->receivers = $receivers;
        $this->supportedTransports = $supportedTransports;
        $this->supportedMessages = $supportedMessages;
    }

    public function checkIfCanProcessMessage(WorkerMessageReceivedEvent $event): void
    {
        if (!$this->messageShouldBeChecked($event)) {
            return;
        }
        $incomingMessage = $this->getIncomingMessageFromEnvelope($event->getEnvelope());

        $event->shouldHandle($this->checker->check($incomingMessage));
        if ($event->shouldHandle()) {
            return;
        }
        $this->receivers[$event->getReceiverName()]->ack($event->getEnvelope());
    }

    public function handleMessageHandledEvent(WorkerMessageHandledEvent $event): void
    {
        if (!$this->messageShouldBeChecked($event)) {
            return;
        }
        $incomingMessage = $this->getIncomingMessageFromEnvelope($event->getEnvelope());

        $this->finalizer->finalizeSuccess($incomingMessage);
    }

    public function handleMessageFailedEvent(WorkerMessageFailedEvent $event): void
    {
        if (!$this->messageShouldBeChecked($event)) {
            return;
        }
        $incomingMessage = $this->getIncomingMessageFromEnvelope($event->getEnvelope());

        if ($event->willRetry()) {
            $this->finalizer->markAsRetry($incomingMessage);
            return;
        }

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

    private function messageShouldBeChecked(AbstractWorkerMessageEvent $event): bool
    {
        if (empty($this->supportedTransports) && empty($this->supportedMessages)) {
            return true;
        }

        if (in_array($event->getReceiverName(), $this->supportedTransports)) {
            return true;
        }

        foreach ($this->supportedMessages as $supportedMessage) {
            if ($event->getEnvelope()->getMessage() instanceof $supportedMessage) {
                return true;
            }
        }

        return false;
    }
}
