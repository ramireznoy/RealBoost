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
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class AdminController extends Controller {

    public function registerAction(Request $request) {
        $response = new JsonResponse();
        try {
            $em = $this->getDoctrine()->getManager();

            $firstname = $request->get('firstname');
            $lastname = $request->get('lastname');
            $email1 = $request->get('email1');
            $email2 = $request->get('email2');

            if (!isset($email1) && !isset($email2) && ($email1 != $email2)) {
                $response->setData(array('success' => false, 'cause' => 'The mail addresses are missing or does not match'));
                return $response;
            }

            $users = $em->getRepository('AdminBundle\Entity\SystemUser')->findBy(array('email' => $email1));
            if (count($users) > 0) {
                $response->setData(array('success' => false, 'cause' => 'Ya existe un usuario registrado con ese correo electrónico.'));
                return $response;
            }

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
    
    public function recoverAction(Request $request) {
        $response = new JsonResponse();
        try {
            $em = $this->getDoctrine()->getManager();
            $email = $request->get('email');
            if (!isset($email) || empty($email)) {
                $response->setData(array('success' => false, 'cause' => 'The mail addresses is missing'));
                return $response;
            }
            $users = $em->getRepository('AdminBundle\Entity\SystemUser')->findBy(array('email' => $email));
            if (count($users) == 0) {
                // Just finish and pretend everything is normal for the user
                $response->setData(array('success' => true));
                return $response;
            }
            $user = $users[0];
            $random_hash = bin2hex(openssl_random_pseudo_bytes(24));
            $user->setAccesstoken($random_hash);
            if ($this->sendResetMail($user)) {
                $response->setData(array('success' => true));
            } else {
                $response->setData(array('success' => false, 'cause' => 'Sorry... The recover service is not available right now. Please try again later'));
            }
            $em->flush();
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
    
    private function sendResetMail($user) {
        $message = \Swift_Message::newInstance()
                ->setContentType("text/html")
                ->setSubject('RealBoost password reset request')
                ->setFrom('registration@realboost.com')
                ->setTo($user->getEmail())
                ->setBody($this->renderView('AdminBundle:Admin:Emails/reset_mail.html.twig', array('user' => $user)));
        $this->get('mailer')->send($message);
        return true;
    }

    public function checkAvailabilityAction(Request $request) {
        try {
            $em = $this->getDoctrine()->getManager();
            $username = $request->get('username');
            $takens = $em->getRepository('AdminBundle\Entity\SystemUser')->findDuplicatedUsername($username);
            if (count($takens) != 0) {
                return new JsonResponse(array(
                    'success' => true,
                    'available' => false
                ));
            } else {
                return new JsonResponse(array(
                    'success' => true,
                    'available' => true
                ));
            }
        } catch (\Exception $ex) {
            return new JsonResponse(array(
                'success' => false,
                'message' => 'There has been an unexpected error: ' . $ex->getMessage(),
                'cause' => $ex->getMessage()
            ));
        }
    }
    
    public function resetAndLoginAction(Request $request) {
        try {
            $em = $this->getDoctrine()->getManager();
            $user_id = $request->get('user');
            if (empty($user_id)) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'Not enough data',
                    'cause' => 'Unable to determine the target user',
                ));
            }
            $user = $em->getRepository('AdminBundle\Entity\SystemUser')->find($user_id);
            if ($user == null) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'Not enough data',
                    'cause' => 'Unable to determine the target user',
                ));
            }
            $accesstoken = $request->get('token');
            if ($user->getAccessToken() != $accesstoken) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'Bad data',
                    'cause' => 'The requested user has no reset pending',
                ));
            }
            $password1 = trim($request->get('password1'));
            $password2 = trim($request->get('password2'));
            if (empty($password1) || empty($password2) || $password1 !== $password2) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'Wrong password',
                    'cause' => 'The passwords were not defined correctly',
                ));
            }
            $password = $this->get('security.password_encoder')->encodePassword($user, $password1);
            $user->setPassword($password);
            $user->setAccessToken(null);

            $em->flush();

            // Log in the new user
            $token = new UsernamePasswordToken($user, $user->getPassword(), "main", $user->getRoles());
            $this->get("security.token_storage")->setToken($token);
            $event = new InteractiveLoginEvent($request, $token);
            $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

            return new JsonResponse(array(
                'success' => true,
                'message' => 'The new user has been created',
                'redirect' => $this->generateUrl('admin_home')
            ));
        } catch (\Exception $ex) {
            return new JsonResponse(array(
                'success' => false,
                'message' => 'An unexpected error has accured.',
                'cause' => $ex->getMessage()
            ));
        }
    }

    public function createAndLoginAction(Request $request) {
        try {
            $em = $this->getDoctrine()->getManager();
            $pre_id = $request->get('pre');
            if (empty($pre_id)) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'Not enough data',
                    'cause' => 'Unable to determine the registration request',
                ));
            }
            $pre = $em->getRepository('AdminBundle\Entity\RegisterRequest')->find($pre_id);
            if ($pre == null) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'Not enough data',
                    'cause' => 'Unable to determine the registration request',
                ));
            }
            $username = $request->get('plaque');
            $takens = $em->getRepository('AdminBundle\Entity\SystemUser')->findDuplicatedUsername($username);
            if (count($takens) != 0) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'Plaque ID not available',
                    'cause' => 'The plaque ID is already taken by another user',
                ));
            }
            $phone = trim($request->get('phone'));
            $takens = $em->getRepository('AdminBundle\Entity\SystemUser')->findDuplicatedPhone($phone);
            if (count($takens) != 0) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'Phone already registered',
                    'cause' => 'The phone number is already registered by another user'
                ));
            }
            $password1 = trim($request->get('password1'));
            $password2 = trim($request->get('password2'));
            if (empty($password1) || empty($password2) || $password1 !== $password2) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'Wrong password',
                    'cause' => 'The passwords were not defined correctly',
                ));
            }
            if (empty($username) || empty($phone) || empty($password1)) {
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'Missing data',
                    'cause' => 'Some required parameters are missing',
                ));
            }
            $group = $em->getRepository('AdminBundle\Entity\UserGroup')->find(CGroups::WORKERS);

            $user = new BusinessWorker();
            $user->setUsername($username);
            $user->setEmail($pre->getEmail());
            $user->setPhone($phone);
            $user->setFirstname($pre->getFirstname());
            $user->setLastname($pre->getLastname());
            $user->setEnabled(true);
            $password = $this->get('security.password_encoder')->encodePassword($user, $password1);
            $user->setPassword($password);
            $user->addGroup($group);

            $em->remove($pre);
            $em->persist($user);
            $em->flush();

            // Log in the new user
            $token = new UsernamePasswordToken($user, $user->getPassword(), "main", $user->getRoles());
            $this->get("security.token_storage")->setToken($token);
            $event = new InteractiveLoginEvent($request, $token);
            $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

            return new JsonResponse(array(
                'success' => true,
                'message' => 'The new user has been created',
                'redirect' => $this->generateUrl('admin_home')
            ));
        } catch (\Exception $ex) {
            return new JsonResponse(array(
                'success' => false,
                'message' => 'An unexpected error has accured.',
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
}
