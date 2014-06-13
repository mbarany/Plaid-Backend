<?php
namespace Barany\Plaid\MainBundle\Controller;

use Barany\Plaid\MainBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AuthenticationController extends BaseController
{
    /**
     * @var \Google_Client
     */
    private $google_client;

    private function isSsl()
    {
        return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'];
    }

    private function setup()
    {
        $this->google_client = new \Google_Client();
        $this->google_client->setClientId($this->container->getParameter('google_client_id'));
        $this->google_client->setClientSecret($this->container->getParameter('google_client_secret'));

        //@todo Use Symfony to determine URL
        $this->google_client->setRedirectUri(
            'http' . ($this->isSsl() ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . '/authentication/oauth'
        );
        $this->google_client->setDeveloperKey($this->container->getParameter('google_developer_key'));
        $this->google_client->setApprovalPrompt('auto');

        $this->google_client->setScopes(array(
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile',
        ));
    }

    /**
     * @Router\Route("/authentication/login")
     * @param Request $request
     * @return Response
     */
    public function login(Request $request)
    {
        $this->setup();
        $access_token = $request->getSession()->get('google_access_token');
        if(!$access_token){
            return $this->redirect($this->google_client->createAuthUrl());
        }
        $this->google_client->setAccessToken($access_token);
        $oauth2 = new \Google_Service_Oauth2($this->google_client);
        $googleUser = $oauth2->userinfo->get();

        /** @var User $user */
        $user = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('Barany\Plaid\MainBundle\Entity\User')
            ->findOneBy(['email' => $googleUser['email']]);
        if ($user) {
            $request->getSession()->set('User', array(
                'id' => $user->getId(),
                'email' => $user->getEmail(),
            ));
        }
        return $this->redirect('/');
    }

    /**
     * @Router\Route("/authentication/oauth")
     * @param Request $request
     * @return Response
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function oauth(Request $request)
    {
        $this->setup();
        if(!isset($_REQUEST['code']) || $_REQUEST['code'] == ''){
            throw new BadRequestHttpException();
        }
        $this->google_client->authenticate($_REQUEST['code']);
        $request->getSession()->set('google_access_token', $this->google_client->getAccessToken());

        return new RedirectResponse('/authentication/login');
    }
}
