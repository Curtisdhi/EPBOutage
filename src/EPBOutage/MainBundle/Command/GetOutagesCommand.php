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
        ->setDescription('Gets outages and stores them in the database')
        ->addOption(
            'use-realtime-threshold',
            null,
            InputOption::VALUE_NONE,
            'If enabled, will add if customers affected are over theshold',
            1
        );
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
        $importer = $this->getContainer()->get('epboutage.outage_importer');
        $importer->importFromJsonApiArray($jsonApi);
        
        if ($input->getOption('use-realtime-threshold')) {
            $thresholds = $this->getContainer()->getParameter('thresholds');
            $customersAffectedThreshold = $thresholds['major_outages']['customers_affected'];
            if ($importer->getObject()->getMetrics()->getCustomersAffected() >= $customersAffectedThreshold) {
                $importer->flush();
            }
        } else {
            $importer->flush();
        }
        
        $output->writeln('Finished');
        
    }
}