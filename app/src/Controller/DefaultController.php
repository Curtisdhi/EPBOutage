<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Uid\Uuid;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Outage;
use DateTime;

class DefaultController extends AbstractController
{


    #[Route("/about", name: "about")]     
    #[Template('pages/about.html.twig')]

    public function aboutAction()
    {
        return [];
    }
    
    #[Route("/ajax/fetch_current_outage", name: "ajax_fetch_current_outage")]
    public function ajaxFetchCurrentOutageData(ManagerRegistry $doctrine): JsonResponse
    {
        $repo = $doctrine->getRepository(Outage::class);        
        //$currentOutage = $repo->findCurrentOutage();
        
        return $this->json($currentOutage);
    }
    
    #[Route("/ajax/fetch_outage/{id}", name: "ajax_fetch_outage", requirements: ['id' => Requirement::UUID])]
    public function ajaxFetchOutageData(ManagerRegistry $doctrine, Uuid $id = null): JsonResponse
    {
        $repo = $doctrine->getRepository(Outage::class);
        $outage = $repo->find($id);
        
        return $this->json($outage);
    }
    
    #[Route("/{id}", name: "main_index", requirements: ['id' => Requirement::UUID])]
    #[Template('pages/index.html.twig')]

    public function indexAction(ManagerRegistry $doctrine, Uuid $id = null)
    {
        // $repo = $doctrine->getRepository(Outage::class);

        // $selectedOutage = $id;
        // $latestOutages = null;
        
        // $hasStartDate = false;
        // $startDate = $request->get('start_date');

        // if (!is_null($startDate)) {
        //     $startDate = (new \Datetime())->setTimestamp($startDate);
        //     $hasStartDate = true;
        // } else {
        //     $startDate = new \DateTime();
        // }
        // $endDate = (new \DateTime($startDate->format('Y-m-d H:i:sP')))->modify('-24hours');
        
        // if ($id !== 0) {
        //     $latestOutages = $repo->findLatestNearId(24, $id);
        //     if (is_null($latestOutages)) {
        //         $this->get('session')->getFlashBag()->set('invalid_id', 'Invalid outage ID!');
        //     }
        // } 
        
        // if (!$latestOutages) {
        //     $latestOutages = $repo->findWithinTimeRange($startDate, $endDate);
        //     reset($latestOutages);
        //     if ($hasStartDate) {
        //         $o = end($latestOutages);
        //         reset($latestOutages);
        //     } else {
        //         $o = current($latestOutages);
        //     }
        //     $selectedOutage = $o['_id'];
        // }
        // $thresholds = $this->getParameter('thresholds');
        // $majorOutages = $repo->findMajorOutages($thresholds['major_outages']['customers_affected']);
        
        // foreach ($latestOutages as $key => $outage) {
        //     $latestOutages[$key]['createdOnFormatted'] = $latestOutages[$key]['createdOn']->toDateTime()
        //             ->setTimezone(new \DateTimeZone(date_default_timezone_get()))->format('M d, Y H:i');
        // }
      
        // return [
        //     'latestOutages' => $latestOutages,
        //     'majorOutages' => $majorOutages,
        //     'selectedOutage' => $selectedOutage,
        //     'startDate' => $startDate,
        // ];
        return [
            'latestOutages' => [],
            'majorOutages' => [],
            'selectedOutage' => null,
            'startDate' => new \DateTime(),
        ];
    }
}
