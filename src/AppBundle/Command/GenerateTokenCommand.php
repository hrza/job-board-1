<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateTokenCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('job:token:generate')
            ->addArgument('jobId', InputArgument::REQUIRED)
            ->setDescription('Generate token for job');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jobId = $input->getArgument('jobId');

        $job = $this->getContainer()->get('job_repository')->findById($jobId);

        if ($job) {
            $token = $this->getContainer()->get('token_generator')->generate($job);

            $table = new Table($output);

            $table->setHeaders(['Token Id'])->addRow([$token->getId()]);

            $table->render();
        }
    }
}
