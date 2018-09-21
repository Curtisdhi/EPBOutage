<?php

namespace EPBOutage\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TestAlertSenderCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
        ->setName('epboutage:sendAlerts')

        // the short description shown while running "php bin/console list"
        ->setDescription('Sends last outage as an email alert');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Sending alerts...');
         
        $repo = $this->getContainer()->get('doctrine_mongodb')
            ->getRepository('EPBOutageMainBundle:Outage');
        
        $outage = $repo->findCurrentOutage();
        
        $alertSender = $this->getContainer()->get('epboutage.alert_sender');
        try {
            $alerts = $alertSender->sendAlerts($outage);
            $output->writeln('Sent out '. $alerts .' alerts.');
        } catch (Exception $ex) {
            $output->writeln('Failed to alert users!');
            $output->writeln($ex->getMessage());
        }
        
        $output->writeln('Finished');
        
    }
    
}