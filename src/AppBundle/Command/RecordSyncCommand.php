<?php

namespace AppBundle\Command;

use AppBundle\Services\Events\ScoreEvent;
use AppBundle\Services\Records\RecordManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RecordSyncCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('orbital:records:sync')
            ->setDescription('Update records by checking all existing scores. Useful after adding new records')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $doctrine = $container->get('doctrine');

        $scoreRepository = $doctrine->getRepository('AppBundle:Score');

        $scores = $scoreRepository->findAll()->getQuery()->execute();

        $scoreCount = count($scores);
        $output->writeln(sprintf('Processing %d score%s...', $scoreCount, $scoreCount == 1 ? '' : 's'));

        $recordListener = $container->get('orbital.scoring.record_listener');

        $count = 0;
        foreach($scores as $score) {
            $newHolders = $recordListener->handleScore(new ScoreEvent($score));
            $count += count($newHolders);
            foreach($newHolders as $newHolder) {
                $output->writeln(sprintf('New holder for \'%s\'', RecordManager::toString($newHolder->getRecord())));
            }
        }

        $output->writeln(sprintf('Completed. Added %d new *potential* record holders, check the approvals list.', $count));
    }
}