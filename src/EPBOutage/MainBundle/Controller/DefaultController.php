<?php

namespace EPBOutage\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        
        
        return $this->render('EPBOutageMainBundle:Default:index.html.twig');
    }
    
}
