<?php

namespace MrAndMrsSmith\IdempotentConsumerSymfonyMessengerBundle\Factory;

use MrAndMrsSmith\IdempotentConsumerBundle\Message\IncomingMessage;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\NonSendableStampInterface;
use Symfony\Component\Serializer\SerializerInterface;

class IncomingMessageFactory
{
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function createFromMessengerMessageEnvelope(Envelope $envelope): IncomingMessage
    {
        $message = $envelope->getMessage();
        $payload = json_decode(
            $this->serializer->serialize($message, 'json'),
            true
        );
        $stamps = $envelope->withoutStampsOfType(NonSendableStampInterface::class);
        $headers = json_decode(
            $this->serializer->serialize($stamps, 'json'),
            true
        );

        return new IncomingMessage($payload, $headers, get_class($message));
    }
}
