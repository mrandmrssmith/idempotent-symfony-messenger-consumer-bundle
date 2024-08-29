<?php

namespace MrAndMrsSmith\IdempotentConsumerSymfonyMessengerBundle\Voter;

use Symfony\Component\Messenger\Event\AbstractWorkerMessageEvent;

class WantToCheckMessageByTransportAndMessageVoter implements WantToCheckMessageVoter
{
    /** @var WantToCheckMessageByTransportVoter */
    private $transportVoter;

    /** @var WantToCheckMessageByMessageVoter */
    private $messageVoter;

    public function __construct(
        WantToCheckMessageByTransportVoter $transportVoter,
        WantToCheckMessageByMessageVoter $messageVoter
    ) {
        $this->transportVoter = $transportVoter;
        $this->messageVoter = $messageVoter;
    }

    public function vote(AbstractWorkerMessageEvent $event): bool
    {
        return $this->transportVoter->vote($event) || $this->messageVoter->vote($event);
    }
}
