<?php

namespace EPBOutage\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    
    /**
     * @Route("/", name="main_index")
     * @Template()
     */
    public function indexAction()
    {
        $repo = $this->get('doctrine_mongodb')
            ->getRepository('EPBOutageMainBundle:Outage');
        $latestOutages = $repo->findLatestWithIdAndUpdatedDate(10);
        
        $majorOutages = $repo->findMajorOutages(1000);
        
        foreach ($latestOutages as $key => $outage) {
            $latestOutages[$key]['updatedOnFormatted'] = $latestOutages[$key]['updatedOn']->toDateTime()
                    ->setTimezone(new \DateTimeZone(date_default_timezone_get()))->format('M d, Y H:i');
        }
        
        return array('latestOutages' => $latestOutages,
            'majorOutages' => $majorOutages);
    }
    
     /**
     * @Route("/about", name="about")
     * @Template()
     */
    public function aboutAction()
    {
        return;
    }
    
    /**
     * @Route("/ajax/fetch_current_outage", name="ajax_fetch_current_outage", options={"expose":true})
     */
    public function ajaxFetchCurrentOutageData() {
        $repo = $this->get('doctrine_mongodb')
            ->getRepository('EPBOutageMainBundle:Outage');
        
        $currentOutage = $repo->findCurrentOutage();
        
        return new JsonResponse($currentOutage);
    }
    
    /**
     * @Route("/ajax/fetch_outage/{id}", name="ajax_fetch_outage", options={"expose":true})
     */
    public function ajaxFetchOutageData($id) {
        $repo = $this->get('doctrine_mongodb')
            ->getRepository('EPBOutageMainBundle:Outage');
        
        $outage = $repo->find($id);
        
        return new JsonResponse($outage);
    }
}
