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
        /** @var \KnpU\OAuth2ClientBundle\Client\Provider\FacebookClient $client */
        $client = $this->get('oauth2.registry')->getClient('linkedin_main');
        try {
            /** @var \League\OAuth2\Client\Provider\LinkedInResourceOwner $user */
            $user = $client->fetchUser();
            // do something with all this new power!
            $user->getEmail();
            // ...
        } catch (IdentityProviderException $e) {
            var_dump($e->getMessage());
            die;
        }
    }

}

