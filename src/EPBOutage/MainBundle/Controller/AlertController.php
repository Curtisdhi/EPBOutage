<?php

namespace EPBOutage\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use EPBOutage\MainBundle\Form\Model\AlertUserModel;
use EPBOutage\MainBundle\Form\Type\AlertUserType;
use EPBOutage\MainBundle\Document\AlertUser;

class AlertController extends Controller
{

    /**
     * @Route("/alert/signup", name="alert_signup",  options={"expose":true})
     * @Template()
     */
    public function signupAction(Request $request, $render = false)
    {
        $form = $this->createForm(new AlertUserType(), new AlertUserModel(), array(
            'action' => $this->generateUrl('alert_signup')
        ));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $alertUser = $form->getData();
            
            $odm = $this->get('doctrine_mongodb')->getManager();
            $repo = $this->get('doctrine_mongodb')->getRepository('EPBOutageMainBundle:AlertUser');

            if ($repo->findOneByEmail($alertUser->getEmail())) {
                $form->get('email')->addError(new \Symfony\Component\Form\FormError("Email has already been used."));
            } else {
                $aUser = new AlertUser();
                $aUser->setEmail($alertUser->getEmail())
                    ->setCustomersAffectedThreshold($alertUser->getCustomersAffectedThreshold());

                $odm->persist($aUser);
                $odm->flush();
                
                return new JsonResponse(array('success' => 'true'));
            }
        }
        
        return array('alertSignupForm' => $form->createView());
    }
    
    /**
     * @Route("/alert/unsubscribe/{id}", name="alert_unsubscribe")
     * @Template()
     */
    public function unsubscribeAction(Request $request, $id)
    {
        $odm = $this->get('doctrine_mongodb')->getManager();
        $repo = $this->get('doctrine_mongodb')->getRepository('EPBOutageMainBundle:AlertUser');
        $alertUser = $repo->findOneById($id);
        if (!$alertUser) {
            throw $this->createNotFoundException('User does not exist');
        } 
        
        $odm->remove($alertUser);
        $odm->flush();
        
        return array();
    }
}