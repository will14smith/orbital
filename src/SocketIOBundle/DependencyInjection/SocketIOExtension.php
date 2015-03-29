<?php

namespace SocketIOBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SocketIOExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->loadClient($config, $container);
    }

    /**
     * Load client form config
     *
     * @param array $config client config
     * @param ContainerBuilder $container client name
     */
    protected function loadClient(array $config, ContainerBuilder $container)
    {
        $definitionIo = new Definition('ElephantIO\Client');
        $definitionIo->setPublic(false);

        switch ($config['version']) {
            case '0.x':
                $versionDefinition = new Definition('ElephantIO\Engine\SocketIO\Version0X');
                break;
            default:
                $versionDefinition = new Definition('SocketIOBundle\Service\Version1X');
                break;
        }

        $versionDefinition->addArgument($config['connection']);
        $versionDefinition->setPublic(false);
        $container->setDefinition('socketio_client.elephantio_version', $versionDefinition);

        $definitionIo->addArgument(new Reference('socketio_client.elephantio_version'));

        $container->setDefinition('socketio_client.elephantio', $definitionIo);

        $definition = new Definition('SocketIOBundle\Service\Client');
        $definition->addArgument(new Reference('socketio_client.elephantio'));

        $container->setDefinition('socketio_client', $definition);
    }
}
