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
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
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
    
    public function userAction() {
        return $this->render('AdminBundle:Admin/BackDoor:user.html.twig');
    }

    public function workerAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $states = $em->getRepository('AdminBundle\Entity\State')->findBy(array('enabled' => true));
        return $this->render('AdminBundle:Admin/BackDoor:worker.html.twig', array('states' => $states));
    }

    public function clientAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $states = $em->getRepository('AdminBundle\Entity\State')->findBy(array('enabled' => true));
        return $this->render('AdminBundle:Admin/BackDoor:client.html.twig', array('states' => $states));
    }

    public function serviceAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $states = $em->getRepository('AdminBundle\Entity\State')->findBy(array('enabled' => true));
        $users = $em->getRepository('CoreBundle\Entity\Client')->findAll();
        $cartypes = $em->getRepository('AdminBundle\Entity\CarType')->findAll();
        $services = $em->getRepository('CoreBundle\Entity\Service')->findBy(array('enabled' => true, 'extra' => false));
        $extras = $em->getRepository('CoreBundle\Entity\Service')->findBy(array('enabled' => true, 'extra' => true));
        return $this->render('AdminBundle:Admin/BackDoor:service.html.twig', array(
                    'states' => $states,
                    'users' => $users,
                    'services' => $services,
                    'extras' => $extras,
                    'cartypes' => $cartypes
        ));
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

    public function servicesAction() {
        $em = $this->getDoctrine()->getManager();
        $services = $em->getRepository('CoreBundle\Entity\Service')->findAll();
        $sales = $em->getRepository('CoreBundle\Entity\Sell')->findAll();

        $table = new TableModel('fa fa-cog', 'Servicios del negocio');
        $columns = array(
            new ColumnModel('20%', 'Nombre'),
            new ColumnModel('30%', 'Descripción'),
            new ColumnModel('10%', 'Tipo'),
            new ColumnModel('10%', 'Popularidad'),
            new ColumnModel('10%', 'Disponible')
        );
        $table->setColumns($columns);
        $table->setActions(array());
        $rows = array();
        foreach ($services as $s) {
            $_u = array();
            $_u[] = $s->getName();
            $_u[] = $s->getDescription();
            $_u[] = HTMLDataView::extraType($s->isExtra());
            $_u[] = HTMLDataView::percentType($s->getSalesCount() / count($sales));
            $_u[] = HTMLDataView::booleanType($s->isEnabled());
            $_u['id'] = $s->getId();
            $rows[] = $_u;
        }
        $table->setNewpath('admin_createservice');
        $table->setEditpath('admin_editservice');
        $table->setDeletepath('admin_deleteservice');
        $table->setData($rows);
        return $this->render('AdminBundle:Admin:Sections/services.html.twig', array('model' => $table));
    }

    public function serviceFormAction(Request $request) {
        try {
            $em = $this->getDoctrine()->getManager();
            $service_id = $request->get('id');
            if (is_null($service_id)) {
                return $this->render('AdminBundle:Forms:service.html.twig');
            } else {
                $service = $em->getRepository('CoreBundle\Entity\Service')->find($service_id);
                if ($service === null) {
                    return $this->render('AdminBundle:Admin:error.html.twig', array('cause' => 'No se encontró el servicio solicitado'));
                } else {
                    return $this->render('AdminBundle:Forms:service.html.twig', array('service' => $service));
                }
            }
        } catch (\Exception $ex) {
            return $this->render('AdminBundle:Admin:error.html.twig', array('cause' => $ex->getMessage()));
        }
    }

    public function categoriesAction() {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('AdminBundle\Entity\CarType')->findAll();

        $table = new TableModel('fa fa-tag', 'Categorías de auto');
        $columns = array(
            new ColumnModel('80%', 'Nombre'),
            new ColumnModel('20%', 'Disponible'),
        );
        $table->setColumns($columns);
        $table->setActions(array());
        $rows = array();
        foreach ($categories as $s) {
            $_u = array();
            $_u[] = $s->getName();
            $_u[] = HTMLDataView::booleanType($s->isEnabled());
            $_u['id'] = $s->getId();
            $rows[] = $_u;
        }
        $table->setNewpath('admin_createcategory');
        $table->setEditpath('admin_editcategory');
        $table->setDeletepath('admin_deletecategory');
        $table->setData($rows);
        return $this->render('AdminBundle:Admin:Sections/categories.html.twig', array('model' => $table));
    }

    public function categoryFormAction(Request $request) {
        try {
            $em = $this->getDoctrine()->getManager();
            $category_id = $request->get('id');
            if (is_null($category_id)) {
                return $this->render('AdminBundle:Forms:category.html.twig');
            } else {
                $category = $em->getRepository('AdminBundle\Entity\CarType')->find($category_id);
                if ($category === null) {
                    return $this->render('AdminBundle:Admin:error.html.twig', array('cause' => 'No se encontró la categoría solicitada'));
                } else {
                    return $this->render('AdminBundle:Forms:category.html.twig', array('category' => $category));
                }
            }
        } catch (\Exception $ex) {
            return $this->render('AdminBundle:Admin:error.html.twig', array('cause' => $ex->getMessage()));
        }
    }

    public function modelsAction() {
        $em = $this->getDoctrine()->getManager();
        $models = $em->getRepository('AdminBundle\Entity\BrandModel')->findAll();

        $table = new TableModel('fa fa-car', 'Modelos de auto');
        $columns = array(
            new ColumnModel('30%', 'Marca'),
            new ColumnModel('30%', 'Modelo'),
            new ColumnModel('30%', 'Tipo'),
        );
        $table->setColumns($columns);
        $table->setActions(array());
        $rows = array();
        foreach ($models as $s) {
            $_u = array();
            $_u[] = $s->getBrand()->getName();
            $_u[] = $s->getName();
            $_u[] = $s->getType()->getName();
            $_u['id'] = $s->getId();
            $rows[] = $_u;
        }
        $table->setNewpath('admin_createmodel');
        $table->setEditpath('admin_editmodel');
        $table->setDeletepath('admin_deletemodel');
        $table->setData($rows);
        return $this->render('AdminBundle:Admin:Sections/models.html.twig', array('model' => $table));
    }

    public function modelFormAction(Request $request) {
        try {
            $em = $this->getDoctrine()->getManager();
            $brands = $em->getRepository('AdminBundle\Entity\CarBrand')->findAll();
            $categories = $em->getRepository('AdminBundle\Entity\CarType')->findAll();
            $model_id = $request->get('id');
            if (is_null($model_id)) {
                return $this->render('AdminBundle:Forms:model.html.twig', array('brands' => $brands, 'categories' => $categories));
            } else {
                $model = $em->getRepository('AdminBundle\Entity\BrandModel')->find($model_id);
                if ($model === null) {
                    return $this->render('AdminBundle:Admin:error.html.twig', array('cause' => 'No se encontró el modelo solicitado'));
                } else {
                    return $this->render('AdminBundle:Forms:model.html.twig', array('model' => $model, 'brands' => $brands, 'categories' => $categories));
                }
            }
        } catch (\Exception $ex) {
            return $this->render('AdminBundle:Admin:error.html.twig', array('cause' => $ex->getMessage()));
        }
    }
    
    public function merchandisesAction() {
        $em = $this->getDoctrine()->getManager();
        $merchandises = $em->getRepository('CoreBundle\Entity\Merchandise')->findAll();

        $table = new TableModel('fa fa-usd', 'Precios de los servicios');
        $columns = array(
            new ColumnModel('30%', 'Servicio'),
            new ColumnModel('20%', 'Tipo de auto'),
            new ColumnModel('10%', 'Precio'),
            new ColumnModel('10%', 'Disponible'),
        );
        $table->setColumns($columns);
        $table->setActions(array());
        $rows = array();
        foreach ($merchandises as $s) {
            $_u = array();
            $_u[] = $s->getService()->getName();
            $_u[] = $s->getCartype()->getName();
            $_u[] = HTMLDataView::moneyType($s->getPrice(), $s->getCurrency());
            $_u[] = HTMLDataView::booleanType($s->isEnabled());            
            $_u['id'] = $s->getId();
            $rows[] = $_u;
        }
        $table->setNewpath('admin_createmerchandise');
        $table->setEditpath('admin_editmerchandise');
        $table->setDeletepath('admin_deletemerchandise');
        $table->setData($rows);
        return $this->render('AdminBundle:Admin:Sections/merchandises.html.twig', array('model' => $table));
    }

    public function merchandiseFormAction(Request $request) {
        try {
            $em = $this->getDoctrine()->getManager();
            $services = $em->getRepository('CoreBundle\Entity\Service')->findBy(array('enabled' => true));
            $categories = $em->getRepository('AdminBundle\Entity\CarType')->findAll();
            $merchandise_id = $request->get('id');
            if (is_null($merchandise_id)) {
                return $this->render('AdminBundle:Forms:merchandise.html.twig', array('services' => $services, 'categories' => $categories));
            } else {
                $merchandise = $em->getRepository('CoreBundle\Entity\Merchandise')->find($merchandise_id);
                if ($merchandise === null) {
                    return $this->render('AdminBundle:Admin:error.html.twig', array('cause' => 'No se encontró la oferta solicitada'));
                } else {
                    return $this->render('AdminBundle:Forms:merchandise.html.twig', array('merchandise' => $merchandise, 'services' => $services, 'categories' => $categories));
                }
            }
        } catch (\Exception $ex) {
            return $this->render('AdminBundle:Admin:error.html.twig', array('cause' => $ex->getMessage()));
        }
    }

}
