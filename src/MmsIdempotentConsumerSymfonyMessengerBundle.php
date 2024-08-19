<?php

namespace MrAndMrsSmith\IdempotentConsumerSymfonyMessengerBundle;

use MrAndMrsSmith\IdempotentConsumerSymfonyMessengerBundle\DependencyInjection\IdempotentConsumerSymfonyMessengerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MmsIdempotentConsumerSymfonyMessengerBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new IdempotentConsumerSymfonyMessengerPass());
    }
}
