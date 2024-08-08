<?php

namespace MrAndMrsSmith\IdempotentConsumerSymfonyMessengerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class IdempotentConsumerSymfonyMessengerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $subscriberDefinition = $container->getDefinition('mms.idempotent_consumer.messenger_bundle.event_subscriber');
        $receivers = $container->findTaggedServiceIds('messenger.receiver');
        
        $mappedReceivers = [];
        foreach ($receivers as $id => $receiver) {
           $mappedReceivers[explode('.', $id)[2]] = new Reference($id); 
        }
        $subscriberDefinition->setArgument('$receivers', $mappedReceivers);
    }
}
