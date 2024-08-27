<?php

namespace MrAndMrsSmith\IdempotentConsumerSymfonyMessengerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class MmsIdempotentConsumerSymfonyMessengerExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources'));
        $loader->load('services.yaml');

        $transportVoterDefinition = $container
            ->getDefinition('mms.idempotent_consumer.messenger_bundle.want_to_check_message_voter.transport');
        $transportVoterDefinition->setArgument('$supportedTransports', $config['supported_transports']);
        $messageVoterDefinition = $container
            ->getDefinition('mms.idempotent_consumer.messenger_bundle.want_to_check_message_voter.message');
        $messageVoterDefinition->setArgument('$supportedMessages', $config['supported_messages']);
        $container->setParameter('mms.idempotent_consumer.messenger_bundle.want_to_check_message_voter.service', $config['voter']);
    }
}
