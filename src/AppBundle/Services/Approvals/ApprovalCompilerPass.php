<?php

namespace AppBundle\Services\Approvals;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ApprovalCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('orbital.approval.manager')) {
            return;
        }

        $definition = $container->findDefinition(
            'orbital.approval.manager'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'orbital.approval.provider'
        );

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall(
                'addProvider', [new Reference($id)]
            );
        }
    }
}
