<?php

namespace CalendarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

    public function indexAction() {
        try {
            $em = $this->getDoctrine()->getManager();
            $items = $em->getRepository('AdminBundle\Entity\MenuItem')->findBy(array('parent' => null));
            $user = $this->getUser();
            return $this->render('CalendarBundle:Default:calendar.html.twig', array('menuitems' => $items, 'user' => $user));
        } catch (\Exception $ex) {
            return $this->render('CalendarBundle:Default:calendar.html.twig', array('resp' => '>>>> fallo <<<< ' . $ex->getMessage()));
        }
    }

}
