<?php

namespace AppBundle;

use AppBundle\Services\Approvals\ApprovalCompilerPass;
use AppBundle\Services\Leagues\LeagueCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ApprovalCompilerPass());
        $container->addCompilerPass(new LeagueCompilerPass());
    }

}
