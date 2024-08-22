<?php

namespace MrAndMrsSmith\IdempotentConsumerSymfonyMessengerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        return new TreeBuilder('mms_idempotent_consumer_symfony_messenger');
    }
}
