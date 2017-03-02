<?php

namespace EPBOutage\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetOutagesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
        ->setName('epboutage:getOutages')

        // the short description shown while running "php bin/console list"
        ->setDescription('Gets outages and stores them in the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Downloading outages');
         
        $loadUrl = $this->getContainer()->getParameter('epb_api_url');
        $json = file_get_contents($loadUrl);
        
        $output->writeln('Parsing outages');
        $this->getContainer()->get('epboutage.outage_importer')->importFromJsonString($json);
        
        $output->writeln('Finished');
        
    }
}