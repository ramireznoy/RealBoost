<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class FacebookController extends Controller {

    public function connectAction() {
        // will redirect to Facebook!
        return $this->get('oauth2.registry')->getClient('facebook_main')->redirect();
    }

    public function connectCheckAction(Request $request) {
        /** @var \KnpU\OAuth2ClientBundle\Client\Provider\FacebookClient $client */
        $client = $this->get('oauth2.registry')->getClient('facebook_main');
        $em = $this->getDoctrine()->getManager();
        try {
            /** @var \League\OAuth2\Client\Provider\FacebookUser $user */
            $socialUser = $client->fetchUser();
            $facebookId = $socialUser->getId();
            $user = $em->getRepository('AdminBundle\Entity\SystemUser')->findFacebookId($facebookId);
            $found = false;
            if ($user == null) {
                $found = false;
            } else {
                $user = $user[0];
                // Here, "public" is the name of the firewall in your security.yml
                $token = new UsernamePasswordToken($user, $user->getPassword(), "main", $user->getRoles());

                // For older versions of Symfony, use security.context here
                $this->get("security.token_storage")->setToken($token);

                // Fire the login event
                // Logging the user in above the way we do it doesn't do this automatically
                $event = new InteractiveLoginEvent($request, $token);
                $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

                $message = new \Swift_Message('Hello Email');
                $message->setFrom('send@example.com');
                $message->setTo('ramireznoy@gmail.com');
                $message->setBody($this->renderView('AdminBundle:Admin:Emails/test.html.twig'),'text/html');

                $this->get('mailer')->send($message);

 
                return $this->redirectToRoute('admin_home');                
            }

            // do something with all this new power!
            return $this->render('AdminBundle:Admin:facebook-brief.html.twig', array('found' => $found, 'type' => 'Facebook', 'user' => $socialUser));
        } catch (\Exception $e) {
            return $this->redirectToRoute('admin_login');
        }
    }

}
