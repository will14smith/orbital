<?php

namespace AppBundle\Services\Leagues;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class LeagueCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('orbital.league.manager')) {
            return;
        }

        $definition = $container->findDefinition(
            'orbital.league.manager'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'orbital.league.algo'
        );

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall(
                'addAlgorithm', [new Reference($id)]
            );
        }
    }
}
