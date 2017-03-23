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
     * @Route("/{id}", name="main_index", defaults={"id" = 0}, options={"expose":true})
     * @Template()
     */
    public function indexAction(Request $request, $id)
    {
        $repo = $this->get('doctrine_mongodb')
            ->getRepository('EPBOutageMainBundle:Outage');
        
        $selectedOutage = $id;
        
        $hasStartDate = false;
        $startDate = $request->get('start_date');
        if (!is_null($startDate)) {
            $startDate = (new \Datetime())->setTimestamp($startDate);
            $hasStartDate = true;
        }
        
        if ($id !== 0) {
            $latestOutages = $repo->findLatestNearId(24, $id);
        } else {
            $latestOutages = $repo->findLatestWithIdAndUpdatedDate(24, $startDate);
            reset($latestOutages);
            if ($hasStartDate) {
                $o = end($latestOutages);
                reset($latestOutages);
            } else {
                $o = current($latestOutages);
            }
            $selectedOutage = $o['_id'];
        }
        $majorOutages = $repo->findMajorOutages(1000);
        
        foreach ($latestOutages as $key => $outage) {
            $latestOutages[$key]['updatedOnFormatted'] = $latestOutages[$key]['updatedOn']->toDateTime()
                    ->setTimezone(new \DateTimeZone(date_default_timezone_get()))->format('M d, Y H:i');
        }
        
        return array('latestOutages' => $latestOutages,
            'majorOutages' => $majorOutages,
            'selectedOutage' => $selectedOutage,
            'startDate' => $startDate);
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
