<?php

namespace MrAndMrsSmith\IdempotentConsumerSymfonyMessengerBundle\Voter;

use Symfony\Component\Messenger\Event\AbstractWorkerMessageEvent;

class WantToCheckMessageByMessageVoter implements WantToCheckMessageVoter
{
    /** @var string[] */
    private $supportedMessages = [];

    /**
     * @param string[] $supportedMessages
     */
    public function __construct(
        array $supportedMessages = []
    ) {
        $this->supportedMessages = $supportedMessages;
    }

    public function vote(AbstractWorkerMessageEvent $event): bool
    {
        if (empty($this->supportedMessages)) {
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
