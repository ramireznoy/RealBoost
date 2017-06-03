<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AdminBundle\Utils\TableModel;
use AdminBundle\Utils\ColumnModel;
use AdminBundle\Utils\HTMLDataView;

class ViewsController extends Controller {
    
    /* BackDoor, just for development */
    public function createSystemUserAction() {
        try {
            return $this->render('AdminBundle:Admin:BackDoor/user.html.twig');
        } catch (\Exception $ex) {
            return $this->render('AdminBundle:Admin:error.html.twig', array('cause' => '>>>> fallo <<<< ' . $ex->getMessage()));
        }
    }

    public function indexAction() {
        try {
            $em = $this->getDoctrine()->getManager();
            $items = $em->getRepository('AdminBundle\Entity\MenuItem')->findBy(array('parent' => null));
            $user = $this->getUser();
            return $this->render('AdminBundle:Admin:index.html.twig', array('menuitems' => $items, 'user' => $user));
        } catch (\Exception $ex) {
            return $this->render('AdminBundle:Admin:error.html.twig', array('cause' => '>>>> fallo <<<< ' . $ex->getMessage()));
        }
    }

    public function loginAction() {
        try {
            if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
                $authenticationUtils = $this->get('security.authentication_utils');
                $error = $authenticationUtils->getLastAuthenticationError();
                return $this->render('AdminBundle:Admin:Login/login.html.twig', array('error' => $error));
            } else {
                return $this->redirectToRoute('admin_home');
            }
        } catch (\Exception $ex) {
            return $this->render('AdminBundle:Admin:error.html.twig', array('cause' => '>>>> fallo <<<< ' . $ex->getMessage()));
        }
    }

    public function recoverAction() {
        try {
            if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
                $authenticationUtils = $this->get('security.authentication_utils');
                $error = $authenticationUtils->getLastAuthenticationError();
                return $this->render('AdminBundle:Admin:Login/recover.html.twig', array('error' => $error));
            } else {
                return $this->redirectToRoute('admin_home');
            }
        } catch (\Exception $ex) {
            return $this->render('AdminBundle:Admin:error.html.twig', array('cause' => '>>>> fallo <<<< ' . $ex->getMessage()));
        }
    }

    public function registrationAction() {
        try {
            if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
                $authenticationUtils = $this->get('security.authentication_utils');
                $error = $authenticationUtils->getLastAuthenticationError();
                return $this->render('AdminBundle:Admin:Login/registration.html.twig', array('error' => $error));
            } else {
                return $this->redirectToRoute('admin_home');
            }
        } catch (\Exception $ex) {
            return $this->render('AdminBundle:Admin:error.html.twig', array('cause' => '>>>> fallo <<<< ' . $ex->getMessage()));
        }
    }

    public function usersAction() {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('AdminBundle\Entity\SystemUser')->findBy(array('enabled' => true));

        $table = new TableModel('fa fa-user', 'Usuarios del Sistema');
        $columns = array(
            new ColumnModel('10%', 'Usuario'),
            new ColumnModel('20%', 'Nombre(s)'),
            new ColumnModel('20%', 'Apellidos'),
            new ColumnModel('20%', 'Correo'),
            new ColumnModel('10%', 'Teléfono'),
            new ColumnModel('20%', 'Tipo')
        );
        $table->setColumns($columns);
        $table->setActions(array());
        $rows = array();
        foreach ($users as $user) {
            $_u = array();
            $_u[] = $user->getUsername();
            $_u[] = $user->getFirstname();
            $_u[] = $user->getLastname();
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
            $paymentgroups = $em->getRepository('CoreBundle\Entity\ClientGroup')->findAll();
            $user_id = $request->get('id');
            if (empty($user_id)) {
                return $this->render('AdminBundle:Forms:user.html.twig', array('groups' => $groups, 'paymentgroups' => $paymentgroups));
            } else {
                $user = $em->getRepository('AdminBundle\Entity\SystemUser')->find($user_id);
                if ($user === null) {
                    return $this->render('AdminBundle:Admin:error.html.twig', array('cause' => 'No se encontró el usuario solicitado'));
                } else {
                    return $this->render('AdminBundle:Forms:user.html.twig', array('user' => $user, 'groups' => $groups, 'paymentgroups' => $paymentgroups));
                }
            }
        } catch (\Exception $ex) {
            return $this->render('AdminBundle:Admin:error.html.twig', array('cause' => $ex->getMessage()));
        }
    }

    public function clientGroupsAction() {
        $em = $this->getDoctrine()->getManager();
        $groups = $em->getRepository('CoreBundle\Entity\Settlement')->findGroups();

        $table = new TableModel('fa fa-group', 'Grupos de Clientes');
        $columns = array(
            new ColumnModel('10%', 'Nombre'),
            new ColumnModel('15%', 'Asesor'),
            new ColumnModel('10%', 'Préstamo'),
            new ColumnModel('10%', 'Pago'),
            new ColumnModel('5%', 'Clientes'),
            new ColumnModel('15%', 'Reunión'),
            new ColumnModel('25%', 'Dirección')
        );
        $table->setColumns($columns);
        $table->setActions(array());
        $rows = array();
        foreach ($groups as $g) {
            $_u = array();
            $_u[] = $g['name'];
            $_u[] = $g['advisor'];
            $_u[] = HTMLDataView::moneyType($g['loan'], 'MXN');
            $_u[] = HTMLDataView::moneyType($g['fee'], 'MXN');
            $_u[] = HTMLDataView::numberType($g['clientcount']);
            $_u[] = HTMLDataView::meetingType($g['day'], $g['time']);
            $_u[] = $g['address'];
            $_u['id'] = $g['id'];
            $rows[] = $_u;
        }
        /* $table->setNewpath('admin_createclientgroup');
          $table->setEditpath('admin_editclientgroup');
          $table->setDeletepath('admin_deleteuser'); */
        $table->setData($rows);
        return $this->render('AdminBundle:Admin:Sections/clientgroups.html.twig', array('model' => $table));
    }

    public function clientGroupFormAction(Request $request) {
        try {
            $em = $this->getDoctrine()->getManager();
            $advisors = $em->getRepository('CoreBundle\Entity\Advisor')->findBy(array('enabled' => true));
            $clientgroup_id = $request->get('id');
            if (empty($clientgroup_id)) {
                return $this->render('AdminBundle:Forms:clientgroup.html.twig', array('advisors' => $advisors));
            } else {
                $clientgroup = $em->getRepository('CoreBundle\Entity\ClientGroup')->find($clientgroup_id);
                if ($clientgroup === null) {
                    return $this->render('AdminBundle:Admin:error.html.twig', array('cause' => 'No se encontró el grupo de usuarios solicitado'));
                } else {
                    return $this->render('AdminBundle:Forms:clientgroup.html.twig', array('clientgroup' => $clientgroup, 'advisors' => $advisors));
                }
            }
        } catch (\Exception $ex) {
            return $this->render('AdminBundle:Admin:error.html.twig', array('cause' => $ex->getMessage()));
        }
    }

}
