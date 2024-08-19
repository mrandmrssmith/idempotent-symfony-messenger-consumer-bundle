<?php

namespace MrAndMrsSmith\IdempotentConsumerSymfonyMessengerBundle\Voter;

use Symfony\Component\Messenger\Event\AbstractWorkerMessageEvent;

interface WantToCheckMessageVoter
{
    public function vote(AbstractWorkerMessageEvent $event): bool;
}
