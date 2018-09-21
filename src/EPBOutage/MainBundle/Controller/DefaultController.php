<?php

namespace EPBOutage\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use EPBOutage\MainBundle\Form\Model\ContactModel;
use EPBOutage\MainBundle\Form\Type\ContactType;

class DefaultController extends Controller
{

     /**
     * @Route("/about", name="about")
     * @Template()
     */
    public function aboutAction(Request $request)
    {
        $form = $this->createForm(new ContactType(), new ContactModel());
        $form->handleRequest($request);
        if ($form->isValid()) {
            $contact = $form->getData();
            
            $recevier =  $this->container->getParameter('mailer_recevier');
            $noreply =  $this->container->getParameter('mailer_noreply');
            
            try {
                $message = \Swift_Message::newInstance()
                    ->setSubject('Contact by '. $contact->getName())
                    ->setFrom($noreply)
                    ->setTo($recevier)
                    ->setContentType("text/html")    
                    ->setBody($this->renderView(
                        'EPBOutageMainBundle:Email:contact.html.twig',
                        array('contact' => $contact)
                    ));
                $this->get('session')->getFlashBag()->set('email_success', 'Thank you for sending us an email! <i class="fa fa-smile-o"></i><br> We shall respond shortly.');
                $this->get('mailer')->send($message);
                
                return $this->redirectToRoute('about');
            }
            catch(\Swift_TransportException $e) {
                $form->addError(new FormError("Failed to send email! If this continues to be a problem, "
                        . "feel free to contact us by other means. Sorry for the inconvenience"));
            }
            
        }
        return array('form' => $form->createView());
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
    
    /**
     * @Route("/{id}", name="main_index", defaults={"id" = 0}, options={"expose":true})
     * @Template()
     */
    public function indexAction(Request $request, $id)
    {
        $repo = $this->get('doctrine_mongodb')
            ->getRepository('EPBOutageMainBundle:Outage');
        
        $selectedOutage = $id;
        $latestOutages = null;
        
        $hasStartDate = false;
        $startDate = $request->get('start_date');

        if (!is_null($startDate)) {
            $startDate = (new \Datetime())->setTimestamp($startDate);
            $hasStartDate = true;
        } else {
            $startDate = new \DateTime();
        }
        $endDate = (new \DateTime($startDate->format('Y-m-d H:i:sP')))->modify('-24hours');
        
        if ($id !== 0) {
            $latestOutages = $repo->findLatestNearId(24, $id);
            if (is_null($latestOutages)) {
                $this->get('session')->getFlashBag()->set('invalid_id', 'Invalid outage ID!');
            }
        } 
        
        if (!$latestOutages) {
            $latestOutages = $repo->findWithinTimeRange($startDate, $endDate);
            reset($latestOutages);
            if ($hasStartDate) {
                $o = end($latestOutages);
                reset($latestOutages);
            } else {
                $o = current($latestOutages);
            }
            $selectedOutage = $o['_id'];
        }
        $thresholds = $this->getParameter('thresholds');
        $majorOutages = $repo->findMajorOutages($thresholds['major_outages']['customers_affected']);
        
        foreach ($latestOutages as $key => $outage) {
            $latestOutages[$key]['createdOnFormatted'] = $latestOutages[$key]['createdOn']->toDateTime()
                    ->setTimezone(new \DateTimeZone(date_default_timezone_get()))->format('M d, Y H:i');
        }
      
        return array('latestOutages' => $latestOutages,
            'majorOutages' => $majorOutages,
            'selectedOutage' => $selectedOutage,
            'startDate' => $startDate);
    }
}
