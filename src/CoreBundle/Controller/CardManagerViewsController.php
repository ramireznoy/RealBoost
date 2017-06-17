<?php

namespace CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class CardManagerViewsController extends Controller
{
    public function indexAction()
    {
        $user = $this->getUser();
        return $this->render('CoreBundle:CardManager:index.html.twig', array('user' => $user));
    }
    
    public function createCardFormAction()
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $templates = $em->getRepository('CoreBundle\Entity\CardTemplate')->findAll();
            return new JsonResponse(array('success' => true, 'data' => $this->renderView('CoreBundle:CardManager:Forms/cardlist.html.twig', array('templates' => $templates))));
        } catch(\Exception $ex) {
            return new JsonResponse(array('success' => false, 'msg' => 'There has been an error trying to make the request', 'cause' => $ex->getMessage()));
        }
    }
}
