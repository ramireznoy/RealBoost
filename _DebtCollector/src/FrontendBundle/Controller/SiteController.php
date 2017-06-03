<?php

namespace FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class SiteController extends Controller
{
    public function indexAction()
    {
        return $this->render('FrontendBundle:Site:index.html.twig');
    }
    
    public function getStatesAction() {
        try {
            $em = $this->getDoctrine()->getEntityManager();
            $states = $em->getRepository('AdminBundle\Entity\State')->findBy(array('enabled' => true));
            $data = array();
            foreach($states as $state) {
                $t = array();
                $t['id'] = $state->getId();
                $t['name'] = $state->getName();
                $data[] = $t;
            }
            return new JsonResponse(array(
                'success' => true,
                'data' => $data
            ));
        } catch (\Exception $ex) {
            return new JsonResponse(array(
                'success' => false,
                'message' => 'No se pudo disponer la lista de estados',
                'cause' => $ex->getMessage()
            ));
        }
    }
}
