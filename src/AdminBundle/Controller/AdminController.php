<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AdminBundle\Entity\SystemUser;
use CoreBundle\Entity\Advisor;
use CoreBundle\Entity\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdminController extends Controller {
    /* BackDoor, just for development */

    public function createSystemUserAction(Request $request) {
        try {
            $em = $this->getDoctrine()->getManager();
            $username = mb_strtolower(trim($request->get('username')), 'UTF-8');
            $firstname = ucwords(trim($request->get('firstname')), 'UTF-8');
            $lastname = ucwords(trim($request->get('lastname')), 'UTF-8');
            $email = mb_strtolower(trim($request->get('email')), 'UTF-8');
            $phone = trim($request->get('phone'));
            /*$takens = $em->getRepository('AdminBundle\Entity\SystemUser')->findDuplicates(array('username' => $username, 'email' => $email, 'phone' => $phone));
            if (count($takens) != 0) {
                $taken = array();
                foreach ($takens as $u) {
                    if ($u->getUsername() === $username) {
                        $taken[] = 'Nombre de usuario';
                    }
                    if ($u->getEmail() === $email) {
                        $taken[] = 'Correo electrónico';
                    }
                    if ($u->getPhone() === $phone) {
                        $taken[] = 'Teléfono';
                    }
                }
                return new JsonResponse(array(
                    'success' => false,
                    'message' => 'Existen usuarios registrados con esos datos en el sistema.',
                    'cause' => 'Debe utilizar otros datos en su registro para [' . implode(', ', $taken) . ']'
                ));
            }*/
            $user = new SystemUser();
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setEnabled(true);
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setPhone($phone);
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
            $em->persist($user);
            $em->flush();
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

    public function editUserAction(Request $request) {
        try {
            $new = false;
            $em = $this->getDoctrine()->getManager();
            $user_id = trim($request->get('id'));
            if (empty($user_id)) {
                $new = true;
                $user_id = 0;
            } else {
                $user = $em->getRepository('AdminBundle\Entity\SystemUser')->find($user_id);
                if ($user == null) {
                    return new JsonResponse(array(
                        'success' => false,
                        'message' => 'No se encontró el usuario para editar.',
                        'cause' => 'El usuario indicado no existe o no cuenta con permiso para su modificación'
                    ));
                }
            }
            $username = mb_strtolower(trim($request->get('username')), 'UTF-8');
            $email = mb_strtolower(trim($request->get('email')), 'UTF-8');
            $phone = trim($request->get('phone'));
            $takens = $em->getRepository('AdminBundle\Entity\SystemUser')->findDuplicates(array('id' => $user_id, 'username' => $username, 'email' => $email, 'phone' => $phone));
            if (count($takens) != 0) {
                $taken = array();
                foreach ($takens as $u) {
                    if ($u->getUsername() === $username) {
                        $taken[] = 'Nombre de usuario';
                    }
                    if ($u->getEmail() === $email) {
                        $taken[] = 'Correo electrónico';
                    }
                    if ($u->getPhone() === $phone) {
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
                        $user = new Advisor();
                        break;
                    case CGroups::USERS:
                        $user = new Client();
                        break;
                }
                $user->addGroup($group);
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
            }
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setPhone($phone);
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setEnabled(true);

            if ($user->getGroup()->getId() == CGroups::USERS) {
                $paymentgroup_id = trim($request->get('paymentgroup'));
                $position_id = trim($request->get('groupposition'));
                $address = mb_convert_case(trim($request->get('address')), MB_CASE_TITLE, 'UTF-8');
                $amount = trim($request->get('amount'));
                $payment = trim($request->get('payment'));

                if (empty($address) || empty($payment) || empty($amount)) {
                    return new JsonResponse(array(
                        'success' => false,
                        'message' => 'Hay datos incompletos en los parámetros del cliente',
                        'cause' => 'Revise los datos de dirección, préstamo y pago mensual.',
                    ));
                }
                $user->setAddress($address);

                if (empty($paymentgroup_id)) {
                    return new JsonResponse(array(
                        'success' => false,
                        'message' => 'Hay datos incompletos.',
                        'cause' => 'El grupo de pago no pudo ser validado o está ausente.',
                    ));
                }
                $paymentgroup = $em->getRepository('CoreBundle\Entity\ClientGroup')->find($paymentgroup_id);
                if ($paymentgroup == null) {
                    return new JsonResponse(array(
                        'success' => false,
                        'message' => 'No se reconoce el grupo de clientes.',
                        'cause' => 'El grupo de clientes del usuario no pudo encontrarse o el parámetro es incorrecto'
                    ));
                }
                $user->setPaymentgroup($paymentgroup);

                if (intval($position_id) > 0 || intval($position_id) < 5) {
                    if ($position_id != '1') {
                        if ($paymentgroup->isPossitionAssigned($position_id)) {
                            return new JsonResponse(array(
                                'success' => false,
                                'message' => 'El cargo no está disponible.',
                                'cause' => 'El cargo del cliente dentro del grupo ya está asignado.',
                            ));
                        }
                    }
                } else {
                    return new JsonResponse(array(
                        'success' => false,
                        'message' => 'Datos incompletos.',
                        'cause' => 'El cargo del cliente no pudo ser identificado o no existe.' . $position_id,
                    ));
                }
                $user->setGroupposition($position_id);
                $user->setAmount($amount);
                $user->setPayment($payment);
            }
            if ($user->getGroup()->getId() == CGroups::WORKERS) {
                $coordinator = mb_convert_case(trim($request->get('coordinator')), MB_CASE_TITLE, 'UTF-8');
                if (empty($coordinator)) {
                    return new JsonResponse(array(
                        'success' => false,
                        'message' => 'Hay datos incompletos en el registro',
                        'cause' => 'El nombre del coordinador no pudo ser validado o está ausente.',
                    ));
                }
                $user->setCoordinator($coordinator);
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
