<?php

namespace App\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class RebuildOutagesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
        ->setName('epboutage:rebuildOutages')

        // the short description shown while running "php bin/console list"
        ->setDescription('Rebuilds outages from the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $batchSize = 100;

        $output->writeln('Rebuilding outages');
        
        $odm = $this->getContainer()->get('doctrine_mongodb');
        $repo = $odm->getRepository('EPBOutageMainBundle:Outage');
        
        $outages = $repo->findAll();
        /*$qb = $odm->getManager()->createQueryBuilder('EPBOutageMainBundle:Outage')
            ->field('importerVersion')->exists(false);
        
        $outages = $qb->getQuery()->execute();*/
        
        $outageCounts = count($outages);
        $i = 0;
        $progress = new ProgressBar($output, $outageCounts);
        $progress->start();
        
        $tempObjects = array();
        
        foreach ($outages as $outage) {
            $json = $outage->getFullJson();
            if ($outage->getImporterVersion() === '1.0.0' || !isset($json['metrics'])) {
                $importer = $this->getContainer()->get('epboutage.old_outage_importer');
            } else {
                $importer = $this->getContainer()->get('epboutage.outage_importer');
            }
            
            $importer->rebuildFromExisting($outage);
            $tempObjects[] = $outage;
            
            if (($i !== 0 && ($i % $batchSize) === 0) || $i >= ($outageCounts - 1)) {
                $progress->setMessage('Batch: '. ($i / $batchSize));
                $odm->getManager()->flush();
                
                // IMPORTANT - clean entities
                foreach($tempObjects as $tempObject) {
                    $odm->getManager()->detach($tempObject);
                }
                
                $odm->getManager()->clear();
                
                gc_enable();
                gc_collect_cycles();
            }

            $progress->advance();
            $i++;
        }
        $output->writeln('');
        $output->writeln('Finished');
        
    }
}
