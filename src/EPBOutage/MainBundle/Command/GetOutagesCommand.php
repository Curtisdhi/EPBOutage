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
         
        $loadUrls = $this->getContainer()->getParameter('epb_api_urls');
        $jsonApi = array();
        foreach ($loadUrls as $key => $url) {
            $jsonApi[$key] = file_get_contents($url);
        }
        
        $output->writeln('Parsing outages');
        $this->getContainer()->get('epboutage.outage_importer')->importFromJsonApiArray($jsonApi);
        
        $output->writeln('Finished');
        
    }
}