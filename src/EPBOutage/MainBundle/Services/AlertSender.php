<?php

namespace EPBOutage\MainBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use \EPBOutage\MainBundle\Document\Outage;

class AlertSender {
    protected $container;
    protected $objectManager;
    protected $mailer;
    protected $templating;
    protected $sendDelay;
     
    public function __construct($container, ObjectManager $objectManager, \Swift_Mailer $mailer, $templating) {
        $this->container = $container;
        $this->objectManager = $objectManager;
        $this->mailer = $mailer;
        $this->templating = $templating;
        $thresholds = $this->container->getParameter('thresholds');
        $this->sendDelay = $thresholds['alert_send_delay'];
    }
    
    public function sendAlerts(Outage $outage) {
        $customersAffected = $outage->getMetrics()->getCustomersAffected();
        $repo = $this->objectManager->getRepository('EPBOutageMainBundle:AlertUser');
        
        $alertUsers = $repo->findByWithinThresholdDelay($customersAffected, $this->sendDelay);
        $countOfUsers = 0;
        
        $message = $this->prepareEmail($outage);       
        
        try {
            foreach ($alertUsers as $aUser) {
                $countOfUsers++;
                
                $message->setTo($aUser->getEmail())
                    ->setBody($this->templating->render(
                    'EPBOutageMainBundle:Email:alert.html.twig',
                    array('outage' => $outage, 'alertUser' => $aUser)
                ));
                
                $this->mailer->send($message);
                
                $aUser->setLastAlertSent(new \DateTime());
            }
            
            $this->objectManager->flush();
        } catch(\Swift_TransportException $e) {
            throw $e;
        }
        
        return $countOfUsers;
    }
    
    private function prepareEmail(Outage $outage) {
        $outage->createdOnFormatted = $outage->getCreatedOn()
            ->setTimezone(new \DateTimeZone(date_default_timezone_get()))->format('M d, Y H:i');        
        $noreply = $this->container->getParameter('mailer_noreply');
        $message = \Swift_Message::newInstance()
            ->setSubject('EPB Power outage Alert')
            ->setFrom($noreply)
            ->setContentType("text/html");
            
        return $message;
    }
    
}
