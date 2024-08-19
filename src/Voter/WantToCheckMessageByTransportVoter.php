<?php

namespace MrAndMrsSmith\IdempotentConsumerSymfonyMessengerBundle\Voter;

use Symfony\Component\Messenger\Event\AbstractWorkerMessageEvent;

class WantToCheckMessageByTransportVoter implements WantToCheckMessageVoter
{
    /** @var string[] */
    private $supportedTransports = [];

    /**
     * @param string[] $supportedTransports
     */
    public function __construct(
        array $supportedTransports = []
    ) {
        $this->supportedTransports = $supportedTransports;
    }

    public function vote(AbstractWorkerMessageEvent $event): bool
    {
        if (empty($this->supportedTransports)) {
            return true;
        }

        if (in_array($event->getReceiverName(), $this->supportedTransports)) {
            return true;
        }

        return false;
    }
}
