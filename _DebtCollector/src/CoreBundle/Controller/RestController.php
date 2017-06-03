<?php

namespace CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AdminBundle\Entity\RegisterRequest;

class RestController extends Controller {

    private function sendInviteMail($registerrequest) {
        try {
            $message = \Swift_Message::newInstance()
                    ->setContentType("text/html")
                    ->setSubject('Solicitud de registro como Usuario')
                    ->setFrom('test@example.com')
                    ->setTo('lramirez@clickypago.com')
                    ->setBody($this->renderView('ClickyPagoAppsMobilePayBundle:Access:registermail.html.twig', array('pre' => $registerrequest)));
            $this->get('mailer')->send($message);
            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }

    private function sendResetMail($user) {
        try {
            $now = new \DateTime();
            $message = \Swift_Message::newInstance()
                    ->setContentType("text/html")
                    ->setSubject('Solicitud de reinicio de Contraseña')
                    ->setFrom('test@example.com')
                    ->setTo('lramirez@clickypago.com')
                    ->setBody($this->renderView('ClickyPagoAppsMobilePayBundle:Access:resetmail.html.twig', array('date' => $now->format('Y-m-d H:m:i'), 'user' => $user)));
            $this->get('mailer')->send($message);
            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function loginAction(Request $request) {
        $response = new JsonResponse();
        $response->headers->set('Access-Control-Allow-Origin', '*');
        try {
            $em = $this->getDoctrine()->getManager();
            $username = $request->get('username');
            if ($username == null || $username == '') {
                $response->setData(array('success' => false, 'cause' => 'Parámetros incorrectos'));
                return $response;
            }
            $users = $em->getRepository('AdminBundle\Entity\SystemUser')->findBy(array('username' => $username));
            if (count($users) == 0) {
                $response->setData(array('success' => false, 'cause' => 'Parámetros incorrectos'));
                return $response;
            }
            $user = $users[0];
            $session = $this->container->get('session');
            $session->start();
            $password = $request->get("password");
            $factory = $this->get('security.encoder_factory');
            $user->setAccessToken($session->getId());
            $encoder = $factory->getEncoder($user);
            $valid = ($encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt())) ? "true" : "false";
            $data = array();
            if ($valid == 'true') {
                $data['success'] = true;
                $data['token'] = $user->getAccessToken();
                $data['profile']['fullname'] = $user->getFullname();
                $data['profile']['email'] = $user->getEmail();
                $data['profile']['notify'] = 'Bienvenido';
            } else {
                $data['success'] = 'false';
            }
            $em->flush();
            $response->setData($data);
            return $response;
        } catch (\Exception $ex) {
            $response->setData(array('success' => false, 'cause' => 'Ocurrió un error mientras se procesaba la solicitud'));
            return $response;
        }
    }

    public function validateTokenAction(Request $request) {
        $response = new JsonResponse();
        $response->headers->set('Access-Control-Allow-Origin', '*');
        try {
            $em = $this->getDoctrine()->getManager();
            $username = $request->get('username');
            if ($username == null || $username == '') {
                $response->setData(array('success' => false, 'cause' => 'Parámetros incorrectos'));
                return $response;
            }
            $_user = $em->getRepository('AdminBundle\Entity\SystemUser')->findBy(array('username' => $username));
            if (count($_user) == 0) {
                $response->setData(array('success' => false, 'cause' => 'Parámetros incorrectos'));
                return $response;
            }
            $user = $_user[0];
            $session = $this->container->get('session');
            $session->start();
            $token = $request->get("token");
            if ($token != $user->getAccessToken()) {
                $response->setData(array('success' => false, 'cause' => 'Parámetros incorrectos'));
                return $response;
            }
            $user->setAccessToken($session->getId());
            $data = array();
            $data['success'] = true;
            $data['token'] = $user->getAccessToken();
            $data['profile']['fullname'] = $user->getFullname();
            $data['profile']['email'] = $user->getEmail();
            $data['profile']['notify'] = 'Bienvenido';
            $em->flush();
            $response->setData($data);
            return $response;
        } catch (\Exception $ex) {
            $response->setData(array('success' => false, 'cause' => 'Ocurrió un error mientras se procesaba la solicitud'));
            return $response;
        }
    }

    public function logoutAction(Request $request) {
        $response = new JsonResponse();
        $response->headers->set('Access-Control-Allow-Origin', '*');
        try {
            $em = $this->getDoctrine()->getManager();
            $username = $request->get('username');
            if ($username == null || $username == '') {
                $response->setData(array('success' => false, 'cause' => 'Parámetros incorrectos'));
                return $response;
            }
            $_user = $em->getRepository('AdminBundle\Entity\SystemUser')->findBy(array('username' => $username));
            if (count($_user) == 0) {
                $response->setData(array('success' => false, 'cause' => 'Parámetros incorrectos'));
                return $response;
            }
            $user = $_user[0];
            $token = $request->get("token");
            if ($token != $user->getAccessToken()) {
                $response->setData(array('success' => false, 'cause' => 'Parámetros incorrectos'));
                return $response;
            }
            $user->setAccessToken('');
            $em->flush();
            $response->setData(array("success" => "true"));
            return $response;
        } catch (\Exception $ex) {
            $response->setData(array('success' => false, 'cause' => 'Ocurrió un error mientras se procesaba la solicitud'));
            return $response;
        }
    }

    public function getPaymentlistAction(Request $request) {
        $response = new JsonResponse();
        $response->headers->set('Access-Control-Allow-Origin', '*');
        try {
            $em = $this->getDoctrine()->getManager();
            $username = $request->get('username');
            if ($username == null || $username == '') {
                $response->setData(array('success' => false, 'cause' => 'Parámetros incorrectos'));
                return $response;
            }
            $_user = $em->getRepository('CoreBundle\Entity\Advisor')->findBy(array('username' => $username));
            if (count($_user) == 0) {
                $response->setData(array('success' => false, 'cause' => 'Parámetros incorrectos'));
                return $response;
            }
            $user = $_user[0];
            $token = $request->get('token');
            if ($token != $user->getAccessToken()) {
                $response->setData(array('success' => false, 'cause' => 'Parámetros incorrectos'));
                return $response;
            }
            $settlements = $em->getRepository('CoreBundle\Entity\Settlement')->findPendingThisWeek($user->getFullname());
            $data = array();
            foreach ($settlements as $s) {
                $t = array();
                $t['time'] = $s['time'];
                $t['id'] = $s['id'];
                $t['name'] = $s['name'];
                $t['tocollect'] = $s['tocollect'];
                $t['unpaids'] = $s['unpaids'];
                $data[$s['day']][] = $t;
            }
            $response->setData(array('success' => true, 'data' => $data));
            return $response;
        } catch (\Exception $ex) {
            $response->setData(array('success' => false, 'cause' => 'Ocurrió un error mientras se procesaba la solicitud ' . $ex->getMessage()));
            return $response;
        }
    }

    public function getClientlistAction(Request $request) {
        $response = new JsonResponse();
        $response->headers->set('Access-Control-Allow-Origin', '*');
        try {
            $em = $this->getDoctrine()->getManager();
            $username = $request->get('username');
            if ($username == null || $username == '') {
                $response->setData(array('success' => false, 'cause' => 'Parámetros incorrectos'));
                return $response;
            }
            $_user = $em->getRepository('CoreBundle\Entity\Advisor')->findBy(array('username' => $username));
            if (count($_user) == 0) {
                $response->setData(array('success' => false, 'cause' => 'Parámetros incorrectos'));
                return $response;
            }
            $user = $_user[0];
            $token = $request->get('token');
            $group = $request->get('group');
            if ($token != $user->getAccessToken() || empty($group)) {
                $response->setData(array('success' => false, 'cause' => 'Parámetros incorrectos'));
                return $response;
            }
            $settlements = $em->getRepository('CoreBundle\Entity\Settlement')->findPendingClientsOnWeek($group);
            $data = array();
            foreach ($settlements as $s) {
                $t = array();
                $t['id'] = $s['id'];
                $t['time'] = $s['time'];
                $t['name'] = $s['name'];
                $t['fee'] = $s['fee'];
                $t['debt'] = $s['debt'];
                $t['address'] = $s['address'];
                $t['clientaddress'] = $s['clientaddress'];
                $t['phone'] = $s['phone'];
                $t['position'] = $s['position'];
                $data[] = $t;
            }
            $response->setData(array('success' => true, 'data' => $data));
            return $response;
        } catch (\Exception $ex) {
            $response->setData(array('success' => false, 'cause' => 'Ocurrió un error mientras se procesaba la solicitud ' . $ex->getMessage()));
            return $response;
        }
    }

    public function updateProfileAction(Request $request) {
        $response = new JsonResponse();
        $response->headers->set('Access-Control-Allow-Origin', '*');
        try {
            $em = $this->getDoctrine()->getManager();
            $username = $request->get('username');
            if ($username == null || $username == '') {
                $response->setData(array('success' => false, 'cause' => 'Parámetros incorrectos'));
                return $response;
            }
            $_user = $em->getRepository('AdminBundle\Entity\SystemUser')->findBy(array('username' => $username));
            if (count($_user) == 0) {
                $response->setData(array('success' => false, 'cause' => 'Parámetros incorrectos'));
                return $response;
            }
            $user = $_user[0];
            $token = $request->get('token');
            if ($token != $user->getAccessToken()) {
                $response->setData(array('success' => false, 'cause' => 'Parámetros incorrectos'));
                return $response;
            }
            $email = $request->get('email');
            $user->setEmail($email);
            $em->flush();
            $response->setData(array('success' => true));
            return $response;
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex) {
            $response->setData(array('success' => false, 'cause' => 'No se pudo actualizar su dirección de correo. Ya está registrada por otro usuario'));
            return $response;
        } catch (\Exception $ex) {
            $response->setData(array('success' => false, 'cause' => 'No se pudo procesar la solicitud'));
            return $response;
        }
    }

    public function getPaymentslistAction(Request $request) {
        $response = new JsonResponse();
        $response->headers->set('Access-Control-Allow-Origin', '*');
        try {
            $em = $this->getDoctrine()->getManager();
            $username = $request->get('username');
            if ($username == null || $username == '') {
                $response->setData(array('success' => false, 'cause' => 'Parámetros incorrectos'));
                return $response;
            }
            $_user = $em->getRepository('AdminBundle\Entity\SystemUser')->findBy(array('username' => $username));
            if (count($_user) == 0) {
                $response->setData(array('success' => false, 'cause' => 'Parámetros incorrectos'));
                return $response;
            }
            $user = $_user[0];
            $token = $request->get('token');
            if ($token != $user->getAccessToken()) {
                $response->setData(array('success' => false, 'cause' => 'Parámetros incorrectos'));
                return $response;
            }
            $startdate = $request->get('startdate') . ' 00:00:00';
            $enddate = $request->get('enddate') . ' 23:59:59';
            $from = new \DateTime($startdate);
            $to = new \DateTime($enddate);
            $salesbyperiod = $em->getRepository('AdminBundle\Entity\Sell')->findByPeriod($user, $from, $to);
            $sales = array();
            $total = 0;
            foreach ($salesbyperiod as $sell) {
                $_m = array();
                $_m['product'] = $sell->getItem()->getName();
                $_m['price'] = $sell->getAmount();
                $_m['currency'] = $sell->getCurrency();
                $_m['timestamp'] = $sell->getDate()->format('Y-m-d H:m:i');
                $sales[] = $_m;
                $total += $sell->getAmount();
            }
            $data = array('total' => $total . ' ' . $user->getCompany()->getCurrency(), 'sales' => $sales);
            $response->setData(array('success' => true, 'data' => $data));
            return $response;
        } catch (\Exception $ex) {
            $response->setData(array('success' => false, 'cause' => 'Ocurrió un error mientras se procesaba la solicitud ' . $ex->getMessage()));
            return $response;
        }
    }

    public function clientChargeAction(Request $request) {
        $response = new JsonResponse();
        $response->headers->set('Access-Control-Allow-Origin', '*');
        try {
            $em = $this->getDoctrine()->getManager();
            $username = $request->get('username');
            if ($username == null || $username == '') {
                $response->setData(array('success' => false, 'cause' => 'Parámetros incorrectos1'));
                return $response;
            }
            $_user = $em->getRepository('AdminBundle\Entity\SystemUser')->findBy(array('username' => $username));
            if (count($_user) == 0) {
                $response->setData(array('success' => false, 'cause' => 'Parámetros incorrectos2'));
                return $response;
            }
            $user = $_user[0];
            $token = $request->get("token");
            if ($token != $user->getAccessToken()) {
                $response->setData(array('success' => false, 'cause' => 'Parámetros incorrectos3'));
                return $response;
            }
            $settlement_id = $request->get('payment-id');
            $settlement = $em->getRepository('CoreBundle\Entity\Settlement')->find($settlement_id);
            if ($settlement == null) {
                $response->setData(array('success' => false, 'cause' => 'Parámetros incorrectos4'));
                return $response;
            }
            $payform = $request->get('payform');
            $settlement->setSettledFor($request->get('payment-amount'));
            $card = $request->get('cardnumber');
            $cvv = $request->get('cvv');
            $mo = $request->get('expirymonth');
            $ye = $request->get('expiryyear');
            $paymentcode = $request->get('payment-code');
            $ticket = $request->get('ticket');

            switch ($payform) {
                case '1':
                    $result = $this->processCardPayment($settlement, $card, $cvv, $mo, $ye);
                    if ($result['code'] == -1) {
                        $response->setData(array('success' => false, 'cause' => 'Error en los datos. ' . $result['cause']));
                        return $response;
                    }
                    if ($result['code'] == 1) {
                        $payed = floatval($settlement->getSettledFor());
                        $debt = floatval($settlement->getClientDebt());
                        $fee = floatval($settlement->getClientFee());
                        if ($payed >= $debt + $fee) {
                            $settlement->setSettled(true);
                        }
                        $settlement->setSettledAt(new \DateTime());
                        $settlement->setConfirmation($result['confirmation']);
                        $settlement->setPaymentForm('Tarjeta');
                        $response->setData(array('success' => true, 'message' => 'La transacción se ejecutó exitosamente'));
                        return $response;
                    } else if ($result['code'] == 2) {
                        $response->setData(array('success' => true, 'message' => 'La transacción está pendiente. Se le hará llegar un correo con el resultado'));
                        return $response;
                    } else if ($result['code'] == 3) {
                        $response->setData(array('success' => false, 'cause' => 'La transacción fue denegada'));
                        return $response;
                    }
                case '2':
                    $payed = floatval($settlement->getSettledFor());
                    $debt = floatval($settlement->getClientDebt());
                    $fee = floatval($settlement->getClientFee());
                    if ($payed >= $debt + $fee) {
                        $settlement->setSettled(true);
                    }
                    $settlement->setSettledAt(new \DateTime());
                    $settlement->setConfirmation($paymentcode);
                    $settlement->setPaymentForm('Oxxo');
                    $data = explode(',', $ticket);
                    file_put_contents('Dossier/oxxo/'.$settlement_id.'.jpg',base64_decode($data[1]));
                    $settlement->setTicketpath('Dossier/oxxo/'.$settlement_id.'.jpg');
                    $response->setData(array('success' => true, 'message' => 'Registrado el pago en Oxxo'));
                    return $response;
                case '3':
                    $payed = floatval($settlement->getSettledFor());
                    $debt = floatval($settlement->getClientDebt());
                    $fee = floatval($settlement->getClientFee());
                    if ($payed >= $debt + $fee) {
                        $settlement->setSettled(true);
                    }
                    $settlement->setSettledAt(new \DateTime());
                    $settlement->setPaymentForm('Efectivo');
                    $response->setData(array('success' => true, 'message' => 'Registrado el pago en efectivo'));
                    return $response;
                default:
                    $response->setData(array('success' => false, 'message' => 'Parámetros incorrectos5'));
                    return $response;
            }
        } catch (\Exception $ex) {
            $response->setData(array('success' => false, 'cause' => 'Ocurrió un error mientras se procesaba la solicitud'));
            return $response;
        } finally {
            $em->flush();
        }
    }

    private function processCardPayment($settlement, $card, $cvv, $mo, $ye) {
        $response_code = array('APROBADA' => 1, 'PENDIENTE' => 2, 'DECLINADA' => 3);
        $baseurl = 'http://localhost/epay/Pruebas/Prueba/secure/soap/ts_REST.php';
        $response = array();
        $response['code'] = -1;
        $data = array();
        $data['clientKey'] = 'ABCDEF1234567890';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $baseurl . '/keyTrans');
        curl_setopt($curl, CURLOPT_POST, sizeof($data));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);
        if ($result != null) {
            $json = json_decode($result, true);
            if ($json['success']) {
                $data = $this->getPostData($settlement, $card, $cvv, $mo, $ye, $json['result'], 'ABCDEF1234567890');
            } else {
                curl_close($curl);
                $response['cause'] = 'No TOKEN';
                return $response;
            }
        } else {
            curl_close($curl);
            $response['cause'] = 'No Token Response';
            return $response;
        }
        curl_close($curl);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $baseurl . '/cardPay');
        curl_setopt($curl, CURLOPT_POST, sizeof($data));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);
        if ($result != null) {
            $json = json_decode($result, true);
            if ($json['success']) {
                $res = $json['result']['Status'];
                $response['code'] = $response_code[$res];
                $response['confirmation'] = $json['result']['IDTrans'];
                return $response;
            } else {
                curl_close($curl);
                $response['cause'] = $json['cause'];
                return $response;
            }
        } else {
            curl_close($curl);
            $response['cause'] = 'No Payment Response';
            return $response;
        }
    }

    private function getPostData($settlement, $card, $cvv, $mo, $ye, $token, $key) {
        $data = array();
        $data['clientKey'] = $key;
        $data['clientToken'] = $token;
        $data['name'] = $settlement->getClientName();
        $data['lastname'] = '';
        $data['mail'] = 'client-' . $settlement->getClientId() . '@no-mail.com';
        $data['phone'] = $settlement->getClientPhone();
        $data['cellphone'] = $settlement->getClientPhone();
        $data['address'] = $settlement->getClientHome();
        $data['zip'] = '12340';
        $data['city'] = 'Monterrey';
        $data['state'] = 'Nuevo León';
        $data['country'] = 'México';
        $data['card'] = $card;
        $data['cvv'] = $cvv;
        $data['mo'] = $mo;
        $data['ye'] = $ye;
        $data['amount'] = $settlement->getSettledFor();
        $data['currency'] = 'MXN';
        return $data;
    }

    public function registerRequestAction(Request $request) {
        $response = new JsonResponse();
        $response->headers->set('Access-Control-Allow-Origin', '*');
        try {
            $em = $this->getDoctrine()->getManager();
            $city_id = $request->get('city');
            $city = $em->getRepository('AdminBundle\Entity\City')->find($city_id);
            if ($city == null) {
                $response->setData(array('success' => false, 'cause' => 'No hay registros de la ciudad seleccionada'));
                return $response;
            }

            $email = $request->get('email');
            $users = $em->getRepository('AdminBundle\Entity\SystemUser')->findBy(array('email' => $email));
            if (count($users) > 0) {
                $response->setData(array('success' => false, 'cause' => 'Esa dirección de correo ya está siendo utilizada'));
                return $response;
            }

            $firstname = $request->get('firstname');
            $lastname = $request->get('lastname');

            $pre = new RegisterRequest();
            $pre->setCity($city);
            $pre->setFirstname($firstname);
            $pre->setLastname($lastname);
            $pre->setEmail($email);

            $random_hash = bin2hex(openssl_random_pseudo_bytes(24));
            $pre->setUrltoken($random_hash);

            if ($this->sendInviteMail($pre)) {
                $em->persist($pre);
                $em->flush();
                $response->setData(array('success' => true));
            } else {
                $response->setData(array('success' => false, 'cause' => 'El servicio de prerregistro no está disponible en estos momentos'));
            }
            return $response;
        } catch (\Exception $ex) {
            $response->setData(array('success' => false, 'cause' => 'Ocurrió un error mientras se procesaba la solicitud'));
            return $response;
        }
    }

    public function resetPasswordAction(Request $request) {
        $response = new JsonResponse();
        $response->headers->set('Access-Control-Allow-Origin', '*');
        try {
            $em = $this->getDoctrine()->getManager();
            $username = $request->get('username');
            $_user = $em->getRepository('AdminBundle\Entity\SystemUser')->findBy(array('username' => $username));
            if (count($_user) == 0) {
                $response->setData(array('success' => false, 'cause' => 'No se encontró ese nombre de usuario'));
                return $response;
            }
            $user = $_user[0];
            $random_hash = bin2hex(openssl_random_pseudo_bytes(24));
            $user->setAccessToken($random_hash);

            if ($this->sendResetMail($user)) {
                $response->setData(array('success' => true));
                $em->flush();
            } else {
                $response->setData(array('success' => false, 'cause' => 'No se pudo enviar correo en estos momentos'));
            }
            return $response;
        } catch (\Exception $ex) {
            $response->setData(array('success' => false, 'cause' => 'Ocurrió un error mientras se procesaba la solicitud'));
            return $response;
        }
    }

}
