<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HandicapSyncCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('orbital:handicap:sync')
            ->setDescription('Remove all handicaps back until the last manual entry (or the beginning). Rebuilds the handicaps using the existing scores.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $doctrine = $container->get('doctrine');

        // get all the people
        $personRepository = $doctrine->getRepository('AppBundle:Person');
        $people = $personRepository->findAll();

        $peopleCount = count($people);
        $output->writeln(sprintf('Processing %d people', $peopleCount));

        $progress = new ProgressBar($output, $peopleCount);
        $progress->start();

        $handicapManager = $container->get('orbital.handicap.manager');
        foreach ($people as $person) {
            $handicapManager->rebuildPerson($person);
            $progress->advance();
        }

        $progress->finish();

        $output->writeln('Completed');
    }
}
