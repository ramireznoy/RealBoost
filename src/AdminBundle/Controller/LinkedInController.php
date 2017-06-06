<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LinkedInController extends Controller {

    public function connectAction() {
        // will redirect to Facebook!
        return $this->get('oauth2.registry')->getClient('linkedin_main')->redirect();
    }

    public function connectCheckAction(Request $request) {
        // ** if you want to *authenticate* the user, then
        // leave this method blank and create a Guard authenticator
        // (read below)

        /** @var \KnpU\OAuth2ClientBundle\Client\Provider\FacebookClient $client */
        $client = $this->get('oauth2.registry')->getClient('linkedin_main');
        try {
            // the exact class depends on which provider you're using
            /** @var \League\OAuth2\Client\Provider\FacebookUser $user */
            $user = $client->fetchUser();
            // do something with all this new power!
            $user->getFirstName();
            // ...
        } catch (IdentityProviderException $e) {
            // something went wrong!
            // probably you should return the reason to the user
            var_dump($e->getMessage());
            die;
        }
    }

}

