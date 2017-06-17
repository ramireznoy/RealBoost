<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AdminBundle\Utils\TableModel;
use AdminBundle\Utils\ColumnModel;
use AdminBundle\Utils\HTMLDataView;

class ViewsController extends Controller {

    public function indexAction() {
        try {
            $em = $this->getDoctrine()->getManager();
            $items = $em->getRepository('AdminBundle\Entity\MenuItem')->findBy(array('parent' => null));
            $user = $this->getUser();
            return $this->render('AdminBundle:Admin:index.html.twig', array('menuitems' => $items, 'user' => $user));
        } catch (\Exception $ex) {
            return $this->render('AdminBundle:Admin:index.html.twig', array('resp' => '>>>> fallo <<<< ' . $ex->getMessage()));
        }
    }

    public function loginFormAction() {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $authenticationUtils = $this->get('security.authentication_utils');
            $error = $authenticationUtils->getLastAuthenticationError();
            return $this->render('AdminBundle:Admin:Login/login.html.twig', array('error' => $error));
        } else {
            return $this->redirectToRoute('admin_home');
        }
    }

    public function registerFormAction() {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $authenticationUtils = $this->get('security.authentication_utils');
            $error = $authenticationUtils->getLastAuthenticationError();
            return $this->render('AdminBundle:Admin:Login/register.html.twig', array('error' => $error));
        } else {
            return $this->redirectToRoute('admin_home');
        }
    }

    public function recoverFormAction() {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $authenticationUtils = $this->get('security.authentication_utils');
            $error = $authenticationUtils->getLastAuthenticationError();
            return $this->render('AdminBundle:Admin:Login/recover.html.twig', array('error' => $error));
        } else {
            return $this->redirectToRoute('admin_home');
        }
    }

    public function resetPasswordAction($token, Request $request) {
        // For the moment, there is no real time limit check like registration process. It will need a new column but I am lazy... ;-)
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('AdminBundle\Entity\SystemUser')->findBy(array('accesstoken' => $token));
        if (count($users) > 0) {
            $user = $users[0];
            return $this->render('AdminBundle:Admin:Login/complete_reset.html.twig', array('user' => $user));
        } else {
            return $this->redirectToRoute('admin_home');
        }
    }

    public function mailConfirmationAction($token, Request $request) {
        $em = $this->getDoctrine()->getManager();
        $_pre = $em->getRepository('AdminBundle\Entity\RegisterRequest')->findBy(array('urltoken' => $token));
        $pre = null;
        if (count($_pre) > 0) {
            $pre = $_pre[0];
            $minimaldate = (new \DateTime('now'))->modify('-3 days');
            if ($pre->getDate() > $minimaldate) {
                return $this->render('AdminBundle:Admin:Login/complete_register.html.twig', array('pre' => $pre));
            }
        }
        if ($pre != null) {
            $em->remove($pre);
            $em->flush();
        }
        return $this->render('AdminBundle:Admin:Login/nopage.html.twig');
    }

    public function usersAction() {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('AdminBundle\Entity\SystemUser')->findBy(array('enabled' => true));

        $table = new TableModel('fa fa-user', 'System Users');
        $columns = array(
            new ColumnModel('20%', 'First name'),
            new ColumnModel('20%', 'Last name'),
            new ColumnModel('10%', 'Plaque'),
            new ColumnModel('10%', 'Email'),
            new ColumnModel('15%', 'Phone'),
            new ColumnModel('15%', 'Type')
        );
        $table->setColumns($columns);
        $table->setActions(array());
        $rows = array();
        foreach ($users as $user) {
            $_u = array();
            $_u[] = $user->getFirstname();
            $_u[] = $user->getLastname();
            $_u[] = $user->getUsername();
            $_u[] = $user->getEmail();
            $_u[] = $user->getPhone();
            $_u[] = $user->getUserType();
            $_u['id'] = $user->getId();
            $rows[] = $_u;
        }
        $table->setNewpath('admin_createuser');
        $table->setEditpath('admin_edituser');
        $table->setDeletepath('admin_deleteuser');
        $table->setData($rows);
        return $this->render('AdminBundle:Admin:Sections/users.html.twig', array('model' => $table));
    }

    public function userFormAction(Request $request) {
        try {
            $em = $this->getDoctrine()->getManager();
            $groups = $em->getRepository('AdminBundle\Entity\UserGroup')->findBy(array('enabled' => true));
            $states = $em->getRepository('AdminBundle\Entity\State')->findBy(array('enabled' => true));
            $user_id = $request->get('id');
            if (is_null($user_id)) {
                return $this->render('AdminBundle:Forms:user.html.twig', array('groups' => $groups, 'states' => $states));
            } else {
                $user = $em->getRepository('AdminBundle\Entity\SystemUser')->find($user_id);
                if ($user === null) {
                    return $this->render('AdminBundle:Admin:error.html.twig', array('cause' => 'No se encontró el usuario solicitado'));
                } else {
                    return $this->render('AdminBundle:Forms:user.html.twig', array('user' => $user, 'groups' => $groups, 'states' => $states));
                }
            }
        } catch (\Exception $ex) {
            return $this->render('AdminBundle:Admin:error.html.twig', array('cause' => $ex->getMessage()));
        }
    }
    
    
}
