<?php

namespace VirtualCardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CardViewController extends Controller
{
    // For the future...
    public function publicRedirectAction($plaque)
    {
        return $this->redirectToRoute('frontend_home');
    }
    
    // For this version, just one business card is allowed
    public function indexAction($plaque)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $users = $em->getRepository('CoreBundle\Entity\BusinessWorker')->findBy(array('username' => $plaque));
            if (count($users) == 0) {
                return $this->redirectToRoute('frontend_home');
            }
            $user = $users[0];
            return $this->render('VirtualCardBundle:Cards:test.html.twig', array('user' => $user));            
        } catch(\Exception $ex) {
            return $this->redirectToRoute('frontend_home');
        }
        
        
        if ($plaque !== 'test') {
            return $this->redirectToRoute('admin_home');
        } else {
            
        }
    }
}
