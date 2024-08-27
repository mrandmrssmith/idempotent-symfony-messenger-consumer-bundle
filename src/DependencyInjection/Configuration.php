<?php

namespace MrAndMrsSmith\IdempotentConsumerSymfonyMessengerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('mms_idempotent_consumer_symfony_messenger');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('supported_transports')
                    ->defaultValue([])
                    ->prototype('scalar')->end()
            ->end()
                ->arrayNode('supported_messages')
                    ->defaultValue([])
                    ->prototype('scalar')->end()
            ->end()
                ->scalarNode('voter')
                ->defaultValue('mms.idempotent_consumer.messenger_bundle.want_to_check_message_voter.transport_and_message')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
