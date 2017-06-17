<?php

namespace CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class CardManagerController extends Controller
{
    public function createCardAction(Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $template_id = $request->get('template');
            if (empty($template_id)) {
                return new JsonResponse(array('success' => false, 'msg' => 'The template was not found'));
            }
            $template = $em->getRepository('CoreBundle\Entity\CardTemplate')->find($template_id);            
            return new JsonResponse(array('success' => true));
        } catch(\Exception $ex) {
            return new JsonResponse(array('success' => false, 'msg' => 'There has been an error trying to make the request', 'cause' => $ex->getMessage()));
        }
    }
}
