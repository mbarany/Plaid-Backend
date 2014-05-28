<?php
namespace Barany\Controller;

use Barany\Core\AppController;
use Barany\Model\User;

class Authentication extends AppController {
    /**
     * @var \Google_Client
     */
    private $google_client;

    private function isSsl(){
        return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'];
    }

    private function setup() {
        $credentials = $this->getAppConfig()->getGoogleCredentials();
        $this->google_client = new \Google_Client();
        $this->google_client->setClientId($credentials['client_id']);
        $this->google_client->setClientSecret($credentials['client_secret']);

        $this->google_client->setRedirectUri(
            'http' . ($this->isSsl() ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . '/authentication/oauth'
        );
        $this->google_client->setDeveloperKey($credentials['developer_key']);
        $this->google_client->setApprovalPrompt('auto');

        $this->google_client->setScopes(array(
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile',
        ));
    }

    public function login(){
        $this->setup();
        $access_token = $this->getSession()->get('google_access_token');
        if(!$access_token){
            $this->redirect($this->google_client->createAuthUrl());
            exit;
        }
        $this->google_client->setAccessToken($access_token);
        $oauth2 = new \Google_Oauth2Service($this->google_client);
        $googleUser = $oauth2->userinfo->get();

        /** @var User $user */
        $user = $this
            ->getEntityManager()
            ->getRepository('Barany\Model\User')
            ->findOneBy(['email' => $googleUser['email']]);
        if ($user) {
            $this->getSession()->set('User', array(
                'id' => $user->getId(),
                'email' => $user->getEmail(),
            ));
        }
        $this->redirect('/');
        exit;
    }

    public function oauth(){
        $this->setup();
        if(!isset($_REQUEST['code']) || $_REQUEST['code'] == ''){
            return;
        }
        $this->google_client->authenticate($_REQUEST['code']);
        $this->getSession()->set('google_access_token', $this->google_client->getAccessToken());
        $this->redirect('/authentication/login');
        exit;
    }
} 