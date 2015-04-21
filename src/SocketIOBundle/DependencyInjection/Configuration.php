<?php

namespace SocketIOBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('socket_io');

        $rootNode
            ->children()
                ->scalarNode('connection')->isRequired()->end()
                ->scalarNode('version')
                    ->defaultValue('1.x')
                    ->validate()
                        ->ifNotInArray(['1.x', '0.x'])
                            ->thenInvalid('Invalid version number "%s"')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
