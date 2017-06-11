<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AdminBundle\Entity\SystemUser;
use CoreBundle\Entity\BusinessWorker;
use CoreBundle\Entity\Client;
use CoreBundle\Entity\Sell;
use CoreBundle\Entity\Service;
use AdminBundle\Entity\CarType;
use AdminBundle\Entity\BrandModel;
use CoreBundle\Entity\Merchandise;
use AdminBundle\Constants\CGroups;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Conekta\Conekta;
use Conekta\Charge;
use AdminBundle\Entity\RegisterRequest;

class AdminController extends Controller {

    public function registerAction(Request $request) {
        $response = new JsonResponse();
        try {
            $em = $this->getDoctrine()->getManager();

            $firstname = $request->get('firstname');
            $lastname = $request->get('lastname');
            $email1 = $request->get('email1');
            $email2 = $request->get('email2');

            if (isset($email1) && isset($email2) && ($email1 != $email2)) {
                $response->setData(array('success' => false, 'cause' => 'The mail addresses are missing or does not match'));
                return $response;
            }

            $users = $em->getRepository('AdminBundle\Entity\SystemUser')->findBy(array('email' => $email1));
            if (count($users) > 0) {
                $response->setData(array('success' => false, 'cause' => 'Ya existe un usuario registrado con ese correo electrónico.'));
                return $response;
            }
            /* $users = $em->getRepository('AdminBundle\Entity\SystemUser')->findBy(array('phone' => $mobile));
              if (count($users) > 0) {
              $response->setData(array('success' => false, 'cause' => 'Ya existe un usuario registrado con ese número telefónico.'));
              return $response;
              } */

            $pre = new RegisterRequest();

            $pre->setFirstname(trim(mb_convert_case($firstname, MB_CASE_TITLE, 'UTF-8')));
            $pre->setLastname(trim(mb_convert_case($lastname, MB_CASE_TITLE, 'UTF-8')));
            $pre->setEmail(trim(strtolower($email1)));
            $random_hash = bin2hex(openssl_random_pseudo_bytes(24));
            $pre->setUrltoken($random_hash);

            $em->persist($pre);
            $em->flush();
            if ($this->sendConfirmationMail($pre)) {
                $response->setData(array('success' => true));
            } else {
                $em->remove($pre);
                $em->flush();
                $response->setData(array('success' => false, 'cause' => 'Sorry... The preregistering service is not available right now. Please try again later'));
            }
            return $response;
        } catch (\Exception $ex) {
            $response->setData(array('success' => false, 'cause' => 'There has been an error while processing the request. ' . $ex->getMessage()));
            return $response;
        }
    }
    
    private function sendConfirmationMail($registerrequest) {
        $message = \Swift_Message::newInstance()
                ->setContentType("text/html")
                ->setSubject('RealBoost register request')
                ->setFrom('registration@realboost.com')
                ->setTo($registerrequest->getEmail())
                ->setBody($this->renderView('AdminBundle:Admin:Emails/register_mail.html.twig', array('pre' => $registerrequest)));
        $this->get('mailer')->send($message);
        return true;
    }
    
    public function createAndLoginUserAction(Request $request) {
        try {
            $em = $this->getDoctrine()->getManager();
            $username = mb_strtolower(trim($request->get('username')), 'UTF-8');
            $email = mb_strtolower(trim($request->get('email')), 'UTF-8');
            $phone = trim($request->get('phone'));
            $takens = $em->getRepository('AdminBundle\Entity\SystemUser')->findDuplicates(array('id' => '0', 'username' => $username, 'email' => $email, 'phone' => $phone));
            if (count($takens) != 0) {
                $taken = array();
                foreach ($takens as $user) {
                    if ($user->getUsername() === $username) {
                        $taken[] = 'Nombre de usuario';
                    }
                    if ($user->getEmail() === $email) {
                        $taken[] = 'Correo electrónico';
                    }
                    if ($user->getPhone() === $phone) {
                        $taken[] = 'Teléfono';
                    }
                }
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'Existen usuarios registrados con esos datos en el sistema.',
                    'cause' => 'Debe utilizar otros datos en su registro para [' . implode(', ', $taken) . ']'
                ));
            }
            if (empty($username)) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'No se reconoce el nombre de usuario',
                    'cause' => 'Debe indicar un nombre de usuario.',
                ));
            }
            $password1 = trim($request->get('password1'));
            $password2 = trim($request->get('password2'));
            if (empty($password1) || empty($password2) || $password1 !== $password2) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'No se reconoce la contraseña',
                    'cause' => 'Las contraseñas no coinciden o no fueron indicadas.',
                ));
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'No se reconoce la dirección de correo electrónico',
                    'cause' => 'No pudo identificarse la dirección de correo electrónico.',
                ));
            }
            $firstname = mb_convert_case(trim($request->get('firstname')), MB_CASE_TITLE, 'UTF-8');
            $lastname = mb_convert_case(trim($request->get('lastname')), MB_CASE_TITLE, 'UTF-8');
            if (empty($username) || empty($phone) || empty($firstname) || empty($lastname)) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'Faltan datos de registros',
                    'cause' => 'No se identificaron algunos datos de registro o estos son incorrectos',
                ));
            }
            $group_id = trim($request->get('group'));
            $group = $em->getRepository('AdminBundle\Entity\UserGroup')->find($group_id);
            if ($group == null) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'No se reconoce el grupo del usuario.',
                    'cause' => 'El grupo del usuario no pudo encontrarse o el parámetro no existe'
                ));
            }

            $user = null;
            switch ($group->getId()) {
                case CGroups::ADMINISTRATORS:
                    $user = new SystemUser();
                    break;
                case CGroups::WORKERS:
                    $user = new BusinessWorker();
                    break;
                case CGroups::USERS:
                    $user = new Client();
                    break;
            }
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setPhone($phone);
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setEnabled(true);
            $password = $this->get('security.password_encoder')->encodePassword($user, $password1);
            $user->setPassword($password);
            $user->addGroup($group);

            if ($group->getId() != CGroups::ADMINISTRATORS) {
                $state_id = trim($request->get('state'));
                $state = $em->getRepository('AdminBundle\Entity\State')->find($state_id);
                if ($state == null) {
                    return new JsonResponse(array(
                        'success' => false,
                        'message' => 'No se reconoce el Estado del usuario.',
                        'cause' => 'El Estado del usuario no pudo encontrarse o el parámetro no existe'
                    ));
                }
                $city = mb_convert_case(trim($request->get('city')), MB_CASE_TITLE, 'UTF-8');
                $address = mb_convert_case(trim($request->get('address')), MB_CASE_TITLE, 'UTF-8');
                $zip = trim($request->get('zip'));
                if (empty($address) || empty($city) || !filter_var($zip, FILTER_VALIDATE_INT)) {
                    return new JsonResponse(array(
                        'success' => false,
                        'message' => 'Hay datos incompletos en la dirección',
                        'cause' => 'La dirección postal contiene información que no pudo ser validada o está ausente.',
                    ));
                }
                $user->setState($state);
                $user->setCity($city);
                $user->setAddress($address);
                $user->setZip($zip);
            }

            $em->persist($user);
            $em->flush();

            return new JsonResponse(array(
                'success' => true,
                'message' => 'Se ha registrado un nuevo usuario'
            ));
        } catch (\Exception $ex) {
            return new JsonResponse(array(
                'success' => false,
                'message' => 'Ha ocurrido un error mientras se procesaba la solicitud.',
                'cause' => $ex->getMessage()
            ));
        }
    }

    public function createUserAction(Request $request) {
        try {
            $em = $this->getDoctrine()->getManager();
            $username = mb_strtolower(trim($request->get('username')), 'UTF-8');
            $email = mb_strtolower(trim($request->get('email')), 'UTF-8');
            $phone = trim($request->get('phone'));
            $takens = $em->getRepository('AdminBundle\Entity\SystemUser')->findDuplicates(array('id' => '0', 'username' => $username, 'email' => $email, 'phone' => $phone));
            if (count($takens) != 0) {
                $taken = array();
                foreach ($takens as $user) {
                    if ($user->getUsername() === $username) {
                        $taken[] = 'Nombre de usuario';
                    }
                    if ($user->getEmail() === $email) {
                        $taken[] = 'Correo electrónico';
                    }
                    if ($user->getPhone() === $phone) {
                        $taken[] = 'Teléfono';
                    }
                }
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'Existen usuarios registrados con esos datos en el sistema.',
                    'cause' => 'Debe utilizar otros datos en su registro para [' . implode(', ', $taken) . ']'
                ));
            }
            if (empty($username)) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'No se reconoce el nombre de usuario',
                    'cause' => 'Debe indicar un nombre de usuario.',
                ));
            }
            $password1 = trim($request->get('password1'));
            $password2 = trim($request->get('password2'));
            if (empty($password1) || empty($password2) || $password1 !== $password2) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'No se reconoce la contraseña',
                    'cause' => 'Las contraseñas no coinciden o no fueron indicadas.',
                ));
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'No se reconoce la dirección de correo electrónico',
                    'cause' => 'No pudo identificarse la dirección de correo electrónico.',
                ));
            }
            $firstname = mb_convert_case(trim($request->get('firstname')), MB_CASE_TITLE, 'UTF-8');
            $lastname = mb_convert_case(trim($request->get('lastname')), MB_CASE_TITLE, 'UTF-8');
            if (empty($username) || empty($phone) || empty($firstname) || empty($lastname)) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'Faltan datos de registros',
                    'cause' => 'No se identificaron algunos datos de registro o estos son incorrectos',
                ));
            }
            $group_id = trim($request->get('group'));
            $group = $em->getRepository('AdminBundle\Entity\UserGroup')->find($group_id);
            if ($group == null) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'No se reconoce el grupo del usuario.',
                    'cause' => 'El grupo del usuario no pudo encontrarse o el parámetro no existe'
                ));
            }

            $user = null;
            switch ($group->getId()) {
                case CGroups::ADMINISTRATORS:
                    $user = new SystemUser();
                    break;
                case CGroups::WORKERS:
                    $user = new BusinessWorker();
                    break;
                case CGroups::USERS:
                    $user = new Client();
                    break;
            }
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setPhone($phone);
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setEnabled(true);
            $password = $this->get('security.password_encoder')->encodePassword($user, $password1);
            $user->setPassword($password);
            $user->addGroup($group);

            if ($group->getId() != CGroups::ADMINISTRATORS) {
                $state_id = trim($request->get('state'));
                $state = $em->getRepository('AdminBundle\Entity\State')->find($state_id);
                if ($state == null) {
                    return new JsonResponse(array(
                        'success' => false,
                        'message' => 'No se reconoce el Estado del usuario.',
                        'cause' => 'El Estado del usuario no pudo encontrarse o el parámetro no existe'
                    ));
                }
                $city = mb_convert_case(trim($request->get('city')), MB_CASE_TITLE, 'UTF-8');
                $address = mb_convert_case(trim($request->get('address')), MB_CASE_TITLE, 'UTF-8');
                $zip = trim($request->get('zip'));
                if (empty($address) || empty($city) || !filter_var($zip, FILTER_VALIDATE_INT)) {
                    return new JsonResponse(array(
                        'success' => false,
                        'message' => 'Hay datos incompletos en la dirección',
                        'cause' => 'La dirección postal contiene información que no pudo ser validada o está ausente.',
                    ));
                }
                $user->setState($state);
                $user->setCity($city);
                $user->setAddress($address);
                $user->setZip($zip);
            }

            $em->persist($user);
            $em->flush();

            return new JsonResponse(array(
                'success' => true,
                'message' => 'Se ha registrado un nuevo usuario'
            ));
        } catch (\Exception $ex) {
            return new JsonResponse(array(
                'success' => false,
                'message' => 'Ha ocurrido un error mientras se procesaba la solicitud.',
                'cause' => $ex->getMessage()
            ));
        }
    }

    public function editUserAction(Request $request) {
        try {
            $new = false;
            $em = $this->getDoctrine()->getManager();
            $user_id = trim($request->get('id'));
            if (empty($user_id)) {
                $new = true;
                $user_id = 0;
            }
            $user = $em->getRepository('AdminBundle\Entity\SystemUser')->find($user_id);
            if ($user == null) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'No se encontró el usuario para editar.',
                    'cause' => 'El usuario indicado no existe o no cuenta con permiso para su modificación'
                ));
            }
            $username = mb_strtolower(trim($request->get('username')), 'UTF-8');
            $email = mb_strtolower(trim($request->get('email')), 'UTF-8');
            $phone = trim($request->get('phone'));
            $takens = $em->getRepository('AdminBundle\Entity\SystemUser')->findDuplicates(array('id' => $user_id, 'username' => $username, 'email' => $email, 'phone' => $phone));
            if (count($takens) != 0) {
                $taken = array();
                foreach ($takens as $user) {
                    if ($user->getUsername() === $username) {
                        $taken[] = 'Nombre de usuario';
                    }
                    if ($user->getEmail() === $email) {
                        $taken[] = 'Correo electrónico';
                    }
                    if ($user->getPhone() === $phone) {
                        $taken[] = 'Teléfono';
                    }
                }
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'Existen usuarios registrados con esos datos en el sistema.',
                    'cause' => 'Debe utilizar otros datos en su registro para [' . implode(', ', $taken) . ']'
                ));
            }
            if (empty($username)) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'No se reconoce el nombre de usuario',
                    'cause' => 'Debe indicar un nombre de usuario.',
                ));
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'No se reconoce la dirección de correo electrónico',
                    'cause' => 'No pudo identificarse la dirección de correo electrónico.',
                ));
            }
            $firstname = mb_convert_case(trim($request->get('firstname')), MB_CASE_TITLE, 'UTF-8');
            $lastname = mb_convert_case(trim($request->get('lastname')), MB_CASE_TITLE, 'UTF-8');
            if (empty($username) || empty($phone) || empty($firstname) || empty($lastname)) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'Faltan datos de registros',
                    'cause' => 'No se identificaron algunos datos de registro o estos son incorrectos',
                ));
            }
            if ($new) {
                $password1 = trim($request->get('password1'));
                $password2 = trim($request->get('password2'));
                if (empty($password1) || empty($password2) || $password1 !== $password2) {
                    return new JsonResponse(array(
                        'success' => false,
                        'message' => 'No se reconoce la contraseña',
                        'cause' => 'Las contraseñas no coinciden o no fueron indicadas.',
                    ));
                }
                $password = $this->get('security.password_encoder')->encodePassword($user, $password1);
                $user->setPassword($password);

                $group_id = trim($request->get('group'));
                $group = $em->getRepository('AdminBundle\Entity\UserGroup')->find($group_id);
                if ($group == null) {
                    return new JsonResponse(array(
                        'success' => false,
                        'message' => 'No se reconoce el grupo del usuario.',
                        'cause' => 'El grupo del usuario no pudo encontrarse o el parámetro no existe'
                    ));
                }
                switch ($group->getId()) {
                    case CGroups::ADMINISTRATORS:
                        $user = new SystemUser();
                        break;
                    case CGroups::WORKERS:
                        $user = new BusinessWorker();
                        break;
                    case CGroups::USERS:
                        $user = new Client();
                        break;
                }
                $user->addGroup($group);
            }
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setPhone($phone);
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setEnabled(true);

            if ($user->getGroup()->getId() != CGroups::ADMINISTRATORS) {
                $state_id = trim($request->get('state'));
                $state = $em->getRepository('AdminBundle\Entity\State')->find($state_id);
                if ($state == null) {
                    return new JsonResponse(array(
                        'success' => false,
                        'message' => 'No se reconoce el Estado del usuario.',
                        'cause' => 'El Estado del usuario no pudo encontrarse o el parámetro no existe'
                    ));
                }
                $city = mb_convert_case(trim($request->get('city')), MB_CASE_TITLE, 'UTF-8');
                $address = mb_convert_case(trim($request->get('address')), MB_CASE_TITLE, 'UTF-8');
                $zip = trim($request->get('zip'));
                if (empty($address) || empty($city) || !filter_var($zip, FILTER_VALIDATE_INT)) {
                    return new JsonResponse(array(
                        'success' => false,
                        'message' => 'Hay datos incompletos en la dirección',
                        'cause' => 'La dirección postal contiene información que no pudo ser validada o está ausente.',
                    ));
                }
                $user->setState($state);
                $user->setCity($city);
                $user->setAddress($address);
                $user->setZip($zip);
            }

            if ($new) {
                $em->persist($user);
            }
            $em->flush();

            if ($new) {
                $message = 'Se ha registrado un nuevo usuario: (<strong>' . $username . '<strong>)';
            } else {
                $message = 'Se ha modificado el usuario <strong>' . $username . '<strong>';
            }

            return new JsonResponse(array(
                'success' => true,
                'message' => $message
            ));
        } catch (\Exception $ex) {
            return new JsonResponse(array(
                'success' => false,
                'message' => 'Ha ocurrido un error mientras se procesaba la solicitud.',
                'cause' => $ex->getMessage()
            ));
        }
    }

    public function deleteUserAction(Request $request) {
        try {
            $em = $this->getDoctrine()->getManager();
            $selection = trim($request->get('selection'));
            if (empty($selection)) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'Nada que borrar',
                    'cause' => 'No se encontraron elementos para borrar'
                ));
            }
            $ids = explode(',', $selection);
            $em->getRepository('AdminBundle\Entity\SystemUser')->setDisabledByIds($ids);
            $em->flush();

            if (count($ids) > 1) {
                $message = 'Se ha eliminado el registro';
            } else {
                $message = 'Se han eliminado los registros';
            }

            return new JsonResponse(array(
                'success' => true,
                'message' => $message
            ));
        } catch (\Exception $ex) {
            return new JsonResponse(array(
                'success' => false,
                'message' => 'Ha ocurrido un error mientras se procesaba la solicitud.',
                'cause' => $ex->getMessage()
            ));
        }
    }

    public function createServiceAction(Request $request) {
        try {
            $em = $this->getDoctrine()->getManager();
            $name = mb_convert_case(trim($request->get('name')), MB_CASE_TITLE, 'UTF-8');
            $description = trim($request->get('description'));
            $extra = trim($request->get('extra'));
            $enabled = trim($request->get('enabled'));

            $takens = $em->getRepository('CoreBundle\Entity\Service')->findDuplicates(array('id' => '0', 'name' => $name));
            if (count($takens) != 0) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'Existen servicios registrados con esos datos en el sistema.',
                    'cause' => 'Debe utilizar otros datos para [Nombre del servicio]'
                ));
            }
            if (empty($name)) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'No se reconoce el nombre del servicio',
                    'cause' => 'Debe indicar un nombre para el servicio.',
                ));
            }
            $service = new Service();
            $service->setName($name);
            $service->setDescription($description);
            if ($enabled === 'true') {
                $service->setEnabled(true);
            } else {
                $service->setEnabled(false);
            }
            if ($extra === 'true') {
                $service->setExtra(true);
            } else {
                $service->setExtra(false);
            }

            $em->persist($service);
            $em->flush();

            return new JsonResponse(array(
                'success' => true,
                'message' => 'Se ha registrado un nuevo servicio'
            ));
        } catch (\Exception $ex) {
            return new JsonResponse(array(
                'success' => false,
                'message' => 'Ha ocurrido un error mientras se procesaba la solicitud.',
                'cause' => $ex->getMessage()
            ));
        }
    }

    public function editServiceAction(Request $request) {
        try {
            $new = false;
            $em = $this->getDoctrine()->getManager();
            $service_id = trim($request->get('id'));
            if (empty($service_id)) {
                $new = true;
                $service_id = 0;
            }
            $service = null;
            if ($new) {
                $service = new Service();
            } else {
                $service = $em->getRepository('CoreBundle\Entity\Service')->find($service_id);
                if ($service == null) {
                    return new JsonResponse(array(
                        'success' => false,
                        'message' => 'No se encontró el servicio para editar.',
                        'cause' => 'El servicio indicado no existe o no cuenta con permiso para su modificación'
                    ));
                }
            }
            $name = mb_convert_case(trim($request->get('name')), MB_CASE_TITLE, 'UTF-8');
            $description = trim($request->get('description'));
            $extra = trim($request->get('extra'));
            $enabled = trim($request->get('enabled'));

            $takens = $em->getRepository('CoreBundle\Entity\Service')->findDuplicates(array('id' => $service_id, 'name' => $name));
            if (count($takens) != 0) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'Existen servicios registrados con esos datos en el sistema.',
                    'cause' => 'Debe utilizar otros datos para [Nombre del servicio]'
                ));
            }
            if (empty($name)) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'No se reconoce el nombre del servicio',
                    'cause' => 'Debe indicar un nombre para el servicio.',
                ));
            }

            $service->setName($name);
            $service->setDescription($description);
            if ($enabled === 'true') {
                $service->setEnabled(true);
            } else {
                $service->setEnabled(false);
            }
            if ($extra === 'true') {
                $service->setExtra(true);
            } else {
                $service->setExtra(false);
            }

            if ($new) {
                $em->persist($service);
            }
            $em->flush();

            if ($new) {
                $message = 'Se ha registrado un nuevo servicio';
            } else {
                $message = 'Se ha modificado el servicio <strong>' . $service->getName() . '</strong>';
            }

            return new JsonResponse(array(
                'success' => true,
                'message' => $message
            ));
        } catch (\Exception $ex) {
            return new JsonResponse(array(
                'success' => false,
                'message' => 'Ha ocurrido un error mientras se procesaba la solicitud.',
                'cause' => $ex->getMessage()
            ));
        }
    }

    public function deleteServiceAction(Request $request) {
        try {
            $em = $this->getDoctrine()->getManager();
            $selection = trim($request->get('selection'));
            if (empty($selection)) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'Nada que borrar',
                    'cause' => 'No se encontraron elementos para borrar'
                ));
            }
            $ids = explode(',', $selection);
            $em->getRepository('CoreBundle\Entity\Service')->deleteByIds($ids);
            $em->flush();

            if (count($ids) > 1) {
                $message = 'Se ha eliminado el registro';
            } else {
                $message = 'Se han eliminado los registros';
            }

            return new JsonResponse(array(
                'success' => true,
                'message' => $message
            ));
        } catch (\Exception $ex) {
            return new JsonResponse(array(
                'success' => false,
                'message' => 'Ha ocurrido un error mientras se procesaba la solicitud.',
                'cause' => $ex->getMessage()
            ));
        }
    }

    public function createCategoryAction(Request $request) {
        try {
            $em = $this->getDoctrine()->getManager();
            $name = mb_convert_case(trim($request->get('name')), MB_CASE_TITLE, 'UTF-8');
            $enabled = trim($request->get('enabled'));

            $takens = $em->getRepository('AdminBundle\Entity\CarType')->findDuplicates(array('id' => '0', 'name' => $name));
            if (count($takens) != 0) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'Existen categorías registradas con esos datos en el sistema.',
                    'cause' => 'Debe utilizar otros datos para [Nombre de la categoría]'
                ));
            }
            if (empty($name)) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'No se reconoce el nombre de la categoría',
                    'cause' => 'Debe indicar un nombre para la categoría.',
                ));
            }
            $type = new CarType();
            $type->setName($name);
            if ($enabled === 'true') {
                $type->setEnabled(true);
            } else {
                $type->setEnabled(false);
            }

            $em->persist($type);
            $em->flush();

            return new JsonResponse(array(
                'success' => true,
                'message' => 'Se ha registrado una nueva categoría'
            ));
        } catch (\Exception $ex) {
            return new JsonResponse(array(
                'success' => false,
                'message' => 'Ha ocurrido un error mientras se procesaba la solicitud.',
                'cause' => $ex->getMessage()
            ));
        }
    }

    public function editCategoryAction(Request $request) {
        try {
            $new = false;
            $em = $this->getDoctrine()->getManager();
            $category_id = trim($request->get('id'));
            if (empty($category_id)) {
                $new = true;
                $category_id = 0;
            }
            $category = null;
            if ($new) {
                $category = new CarType();
            } else {
                $category = $em->getRepository('AdminBundle\Entity\CarType')->find($category_id);
                if ($category == null) {
                    return new JsonResponse(array(
                        'success' => false,
                        'message' => 'No se encontró la categoría para editar.',
                        'cause' => 'La categoría indicada no existe o no cuenta con permiso para su modificación'
                    ));
                }
            }
            $name = mb_convert_case(trim($request->get('name')), MB_CASE_TITLE, 'UTF-8');
            $enabled = trim($request->get('enabled'));

            $takens = $em->getRepository('AdminBundle\Entity\CarType')->findDuplicates(array('id' => $category_id, 'name' => $name));
            if (count($takens) != 0) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'Existen categorías registradas con esos datos en el sistema.',
                    'cause' => 'Debe utilizar otros datos para [Nombre de la categoría]'
                ));
            }
            if (empty($name)) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'No se reconoce el nombre de la categoría',
                    'cause' => 'Debe indicar un nombre para la categoría.',
                ));
            }

            $category->setName($name);
            if ($enabled === 'true') {
                $category->setEnabled(true);
            } else {
                $category->setEnabled(false);
            }

            if ($new) {
                $em->persist($category);
            }
            $em->flush();

            if ($new) {
                $message = 'Se ha registrado un nuevo servicio';
            } else {
                $message = 'Se ha modificado el servicio <strong>' . $category->getName() . '</strong>';
            }

            return new JsonResponse(array(
                'success' => true,
                'message' => $message
            ));
        } catch (\Exception $ex) {
            return new JsonResponse(array(
                'success' => false,
                'message' => 'Ha ocurrido un error mientras se procesaba la solicitud.',
                'cause' => $ex->getMessage()
            ));
        }
    }

    public function deleteCategoryAction(Request $request) {
        try {
            $em = $this->getDoctrine()->getManager();
            $selection = trim($request->get('selection'));
            if (empty($selection)) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'Nada que borrar',
                    'cause' => 'No se encontraron elementos para borrar'
                ));
            }
            $ids = explode(',', $selection);
            $em->getRepository('AdminBundle\Entity\CarType')->deleteByIds($ids);
            $em->flush();

            if (count($ids) > 1) {
                $message = 'Se han eliminado los registros';
            } else {
                $message = 'Se ha eliminado el registro';
            }

            return new JsonResponse(array(
                'success' => true,
                'message' => $message
            ));
        } catch (\Exception $ex) {
            return new JsonResponse(array(
                'success' => false,
                'message' => 'Ha ocurrido un error mientras se procesaba la solicitud.',
                'cause' => $ex->getMessage()
            ));
        }
    }

    public function createModelAction(Request $request) {
        try {
            $em = $this->getDoctrine()->getManager();
            $name = mb_convert_case(trim($request->get('name')), MB_CASE_LOWER, 'UTF-8');

            $brand_id = trim($request->get('brand'));
            $brand = $em->getRepository('AdminBundle\Entity\CarBrand')->find($brand_id);
            if ($brand == null) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'No se reconoce la marca del auto.',
                    'cause' => 'La marca del auto no pudo encontrarse o el parámetro no existe'
                ));
            }
            $type_id = trim($request->get('category'));
            $type = $em->getRepository('AdminBundle\Entity\CarType')->find($type_id);
            if ($type == null) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'No se reconoce la categoría del auto.',
                    'cause' => 'La categoría del auto no pudo encontrarse o el parámetro no existe'
                ));
            }
            $takens = $em->getRepository('AdminBundle\Entity\BrandModel')->findDuplicates(array('id' => '0', 'name' => $name, 'brand' => $brand, 'type' => $type));
            if (count($takens) != 0) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'El auto ya se encuentra registrado.',
                    'cause' => 'Ese modelo de auto ya está registrado en el sistema'
                ));
            }
            if (empty($name)) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'No se reconoce el modelo de auto',
                    'cause' => 'Debe indicar el nombre del modelo de auto.',
                ));
            }
            $model = new BrandModel();
            $model->setName($name);
            $model->setBrand($brand);
            $model->setType($type);

            $em->persist($model);
            $em->flush();

            return new JsonResponse(array(
                'success' => true,
                'message' => 'Se ha registrado el nuevo modelo de auto'
            ));
        } catch (\Exception $ex) {
            return new JsonResponse(array(
                'success' => false,
                'message' => 'Ha ocurrido un error mientras se procesaba la solicitud.',
                'cause' => $ex->getMessage()
            ));
        }
    }

    public function editModelAction(Request $request) {
        try {
            $new = false;
            $em = $this->getDoctrine()->getManager();
            $model_id = trim($request->get('id'));
            if (empty($model_id)) {
                $new = true;
                $model_id = 0;
            }
            $model = null;
            if ($new) {
                $model = new BrandModel();
            } else {
                $model = $em->getRepository('AdminBundle\Entity\BrandModel')->find($model_id);
                if ($model == null) {
                    return new JsonResponse(array(
                        'success' => false,
                        'message' => 'No se reconoce el modelo de auto',
                        'cause' => 'Debe indicar el nombre del modelo de auto.',
                    ));
                }
            }
            $name = mb_convert_case(trim($request->get('name')), MB_CASE_LOWER, 'UTF-8');
            $brand_id = trim($request->get('brand'));
            $brand = $em->getRepository('AdminBundle\Entity\CarBrand')->find($brand_id);
            if ($brand == null) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'No se reconoce la marca del auto.',
                    'cause' => 'La marca del auto no pudo encontrarse o el parámetro no existe'
                ));
            }
            $type_id = trim($request->get('category'));
            $type = $em->getRepository('AdminBundle\Entity\CarType')->find($type_id);
            if ($type == null) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'No se reconoce la categoría del auto.',
                    'cause' => 'La categoría del auto no pudo encontrarse o el parámetro no existe'
                ));
            }
            $takens = $em->getRepository('AdminBundle\Entity\BrandModel')->findDuplicates(array('id' => '0', 'name' => $name, 'brand' => $brand, 'type' => $type));
            if (count($takens) != 0) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'El auto ya se encuentra registrado.',
                    'cause' => 'Ese modelo de auto ya está registrado en el sistema'
                ));
            }
            if (empty($name)) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'No se reconoce el modelo de auto',
                    'cause' => 'Debe indicar el nombre del modelo de auto.',
                ));
            }
            $model->setName($name);
            $model->setBrand($brand);
            $model->setType($type);

            if ($new) {
                $em->persist($model);
            }
            $em->flush();

            if ($new) {
                $message = 'Se ha registrado el nuevo modelo de auto';
            } else {
                $message = 'Se ha modificado el modelo ' . $model->getFullName();
            }

            return new JsonResponse(array(
                'success' => true,
                'message' => $message
            ));
        } catch (\Exception $ex) {
            return new JsonResponse(array(
                'success' => false,
                'message' => 'Ha ocurrido un error mientras se procesaba la solicitud.',
                'cause' => $ex->getMessage()
            ));
        }
    }

    public function deleteModelAction(Request $request) {
        try {
            $em = $this->getDoctrine()->getManager();
            $selection = trim($request->get('selection'));
            if (empty($selection)) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'Nada que borrar',
                    'cause' => 'No se encontraron elementos para borrar'
                ));
            }
            $ids = explode(',', $selection);
            $em->getRepository('AdminBundle\Entity\BrandModel')->deleteByIds($ids);
            $em->flush();

            if (count($ids) > 1) {
                $message = 'Se han eliminado los registros';
            } else {
                $message = 'Se ha eliminado el registro';
            }

            return new JsonResponse(array(
                'success' => true,
                'message' => $message
            ));
        } catch (\Exception $ex) {
            return new JsonResponse(array(
                'success' => false,
                'message' => 'Ha ocurrido un error mientras se procesaba la solicitud.',
                'cause' => $ex->getMessage()
            ));
        }
    }

    public function editMerchandiseAction(Request $request) {
        try {
            $new = false;
            $em = $this->getDoctrine()->getManager();
            $merchandise_id = trim($request->get('id'));
            if (empty($merchandise_id)) {
                $new = true;
                $merchandise_id = 0;
            }
            $merchandise = null;
            if ($new) {
                $merchandise = new Merchandise();
            } else {
                $merchandise = $em->getRepository('CoreBundle\Entity\Merchandise')->find($merchandise_id);
                if ($merchandise == null) {
                    return new JsonResponse(array(
                        'success' => false,
                        'message' => 'No se reconoce la oferta',
                        'cause' => 'La oferta indicada no es válida o no tiene permisos para modificarla.',
                    ));
                }
            }
            $price = trim($request->get('price'));
            $service_id = trim($request->get('service'));
            $service = $em->getRepository('CoreBundle\Entity\Service')->find($service_id);
            if ($service == null) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'No se reconoce el servicio.',
                    'cause' => 'No pudo encontrarse el servicio o el parámetro no existe'
                ));
            }
            $type_id = trim($request->get('category'));
            $type = $em->getRepository('AdminBundle\Entity\CarType')->find($type_id);
            if ($type == null) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'No se reconoce la categoría del auto.',
                    'cause' => 'La categoría del auto no pudo encontrarse o el parámetro no existe'
                ));
            }

            $takens = $em->getRepository('CoreBundle\Entity\Merchandise')->findDuplicates(array('id' => $merchandise_id, 'service' => $service, 'cartype' => $type));
            if (count($takens) != 0) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'La oferta ya está registrada.',
                    'cause' => 'Ya existe una oferta previa para esa combinación de servicio y tipo de auto.'
                ));
            }

            if (empty($price) || !filter_var($price, FILTER_VALIDATE_FLOAT)) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'No se reconoce el precio de la oferta',
                    'cause' => 'Debe indicar el precio de la oferta.',
                ));
            }
            $enabled = trim($request->get('enabled'));
            if ($enabled === 'true') {
                $merchandise->setEnabled(true);
            } else {
                $merchandise->setEnabled(false);
            }

            $merchandise->setPrice(number_format((float) $price, 2, '.', ''));
            $merchandise->setCurrency('MXN');
            $merchandise->setCartype($type);
            $merchandise->setService($service);

            if ($new) {
                $em->persist($merchandise);
            }
            $em->flush();

            if ($new) {
                $message = 'Se ha registrado una nueva oferta de servicio';
            } else {
                $message = 'Se ha modificado la oferta de servicio';
            }

            return new JsonResponse(array(
                'success' => true,
                'message' => $message
            ));
        } catch (\Exception $ex) {
            return new JsonResponse(array(
                'success' => false,
                'message' => 'Ha ocurrido un error mientras se procesaba la solicitud.',
                'cause' => $ex->getMessage()
            ));
        }
    }

    public function deleteMerchandiseAction(Request $request) {
        try {
            $em = $this->getDoctrine()->getManager();
            $selection = trim($request->get('selection'));
            if (empty($selection)) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'Nada que borrar',
                    'cause' => 'No se encontraron elementos para borrar'
                ));
            }
            $ids = explode(',', $selection);
            $em->getRepository('CoreBundle\Entity\Merchandise')->deleteByIds($ids);
            $em->flush();

            if (count($ids) > 1) {
                $message = 'Se han eliminado los registros';
            } else {
                $message = 'Se ha eliminado el registro';
            }

            return new JsonResponse(array(
                'success' => true,
                'message' => $message
            ));
        } catch (\Exception $ex) {
            return new JsonResponse(array(
                'success' => false,
                'message' => 'Ha ocurrido un error mientras se procesaba la solicitud.',
                'cause' => $ex->getMessage()
            ));
        }
    }

    public function sellServiceAction(Request $request) {
        try {
            $em = $this->getDoctrine()->getManager();
            $client_id = trim($request->get('user'));
            $client = $em->getRepository('CoreBundle\Entity\Client')->find($client_id);
            if ($client == null) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'No se reconocen el usuario',
                    'cause' => 'No se pudo identificar al usuario con los datos suministrados.',
                ));
            }
            $service_id = trim($request->get('service'));
            $service = $em->getRepository('CoreBundle\Entity\Service')->find($service_id);
            if ($service == null) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'No se reconocen el servicio',
                    'cause' => 'No se pudo identificar al servicio con los datos suministrados.',
                ));
            }
            $cartype_id = trim($request->get('cartype'));
            $cartype = $em->getRepository('AdminBundle\Entity\CarType')->find($cartype_id);
            if ($cartype == null) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'No se reconoce el tipo de auto',
                    'cause' => 'No se pudo identificar el tipo de auto con los datos suministrados.',
                ));
            }
            $main_service = $em->getRepository('CoreBundle\Entity\Merchandise')->findOneBy(array('service' => $service, 'cartype' => $cartype, 'enabled' => true));
            if ($main_service == null) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'No se reconocen el tipo de servicio solicitado',
                    'cause' => 'No se pudo identificar el tipo de servicio o este se encuentra deshabilitado.',
                ));
            }
            $extras = array();
            $extra_service_ids = $request->get('extras');
            if ($extra_service_ids != null) {
                foreach ($extra_service_ids as $extra_id) {
                    $extra_service = $em->getRepository('CoreBundle\Entity\Service')->find($extra_id);
                    if ($extra_service != null) {
                        if ($extra_service->isEnabled()) {
                            $extra_merchandise = $em->getRepository('CoreBundle\Entity\Merchandise')->findOneBy(array('service' => $extra_service, 'cartype' => $cartype, 'enabled' => true));
                            if ($extra_merchandise != null) {
                                $extras[] = $extra_merchandise;
                            }
                        }
                    }
                }
            }
            $datetime = trim($request->get('datetime'));
            $address = mb_convert_case(trim($request->get('address')), MB_CASE_TITLE, 'UTF-8');
            $city = mb_convert_case(trim($request->get('city')), MB_CASE_TITLE, 'UTF-8');
            $state_name = mb_convert_case(trim($request->get('state')), MB_CASE_TITLE, 'UTF-8');
            $state = $em->getRepository('AdminBundle\Entity\State')->findOneBy(array('name' => $state_name, 'enabled' => true));
            if ($state == null) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'No se reconoce el Estado en la dirección',
                    'cause' => 'No se pudo identificar al Estado de la dirección indicada o este no está disponible para el servicio.',
                ));
            }
            $zip = trim($request->get('zip'));
            $latitude = trim($request->get('latitude'));
            $longitude = trim($request->get('longitude'));

            if (is_null($latitude) || is_null($longitude) || is_null($datetime) || is_null($address) || is_null($city) || is_null($state)) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'No se reconocen la ubicación',
                    'cause' => 'Algunos datos de la localización no pudieron validarse o están incompletos',
                ));
            }
            $scheduled = new \DateTime($datetime);

            $sell = new Sell();
            $sell->setAddress($address);
            $sell->setCity($city);
            $sell->setClient($client);
            $sell->setLatitude($latitude);
            $sell->setLongitude($longitude);
            $sell->setScheduled($scheduled);
            $sell->setState($state);
            $sell->setZip($zip);
            $sell->setCompleted(false);
            $sell->setPayed(false);
            $sell->addItem($main_service);
            foreach ($extras as $service) {
                $sell->addItem($service);
            }

            $em->persist($sell);
            $em->flush();

            return new JsonResponse(array(
                'success' => true,
                'message' => 'Se ha registrado un nuevo servicio',
            ));
        } catch (\Exception $ex) {
            return new JsonResponse(array(
                'success' => false,
                'message' => 'Ha ocurrido un error mientras se procesaba la solicitud.',
                'cause' => $ex->getMessage()
            ));
        }
    }

    public function testAction() {
        Conekta::setApiKey("key_eYvWV7gSDkNYXsmr");
        Conekta::setLocale('es');

        $response = 'Proceding...';

        try {
            $charge = Charge::create(array(
                        'description' => 'Stogies',
                        'reference_id' => '9839-wolf_pack',
                        'amount' => 20000,
                        'currency' => 'MXN',
                        'cash' => array(
                            'type' => 'oxxo'
                        ),
                        'details' => array(
                            'name' => 'Arnulfo Quimare',
                            'phone' => '403-342-0642',
                            'email' => 'logan@x-men.org',
                            /* 'customer' => array(
                              'corporation_name' => 'Conekta Inc.',
                              'logged_in' => true,
                              'successful_purchases' => 14,
                              'created_at' => 1379784950,
                              'updated_at' => 1379784950,
                              'offline_payments' => 4,
                              'score' => 9
                              ), */
                            'line_items' => array(
                                array(
                                    'name' => 'Box of Cohiba S1s',
                                    'description' => 'Imported From Mex.',
                                    'unit_price' => 20000,
                                    'quantity' => 1,
                                    'sku' => 'cohb_s1',
                                    'type' => 'food'
                                )
                            ),
                            'billing_address' => array(
                                'street1' => '77 Mystery Lane',
                                'street2' => 'Suite 124',
                                'street3' => null,
                                'city' => 'Darlington',
                                'state' => 'NJ',
                                'zip' => '10192',
                                'country' => 'Mexico',
                                'phone' => '77-777-7777',
                                'email' => 'purshasing@x-men.org'
                            )
                        )
            ));
            $response = $charge->payment_method->barcode_url;
        } catch (Conekta_Error $e) {
            $response = $e->message_to_purchaser;
        } finally {
            return $this->render('AdminBundle:Admin:index.html.twig', array('resp' => $response));
        }
    }

}
