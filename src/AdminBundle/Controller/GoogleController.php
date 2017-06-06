<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GoogleController extends Controller {

    public function connectAction() {
        // will redirect to Google!
        return $this->get('oauth2.registry')->getClient('google_main')->redirect();
    }

    public function connectCheckAction(Request $request) {
        /** @var \KnpU\OAuth2ClientBundle\Client\Provider\GoogleClient $client */
        $client = $this->get('oauth2.registry')->getClient('google_main');
        try {
            /** @var \League\OAuth2\Client\Provider\GoogleUser $user */
            $user = $client->fetchUser();
            // do something with all this new power!
            $user->getFirstName();
            // ...
        } catch (IdentityProviderException $e) {
            var_dump($e->getMessage());
            die;
        }
    }

}
