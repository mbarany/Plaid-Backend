<?php
namespace Barany\Plaid\MainBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends BaseController {

    /**
     * @Router\Route("/")
     * @Router\Template
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request) {
        if (!$request->getSession()->has('User')) {
            return new RedirectResponse('/authentication/login');
        }
        return $this->render('BaranyPlaidMainBundle:Index:index.html.twig');
    }

    public function connect() {
exit;
        $request = Request::post(
            $this->getAppConfig()->getPlaidApiEndpoint() . 'connect',
            array_merge(
                $this->getAppConfig()->getPlaidApiCredentials(),
                array(
                    'credentials' =>array(
//                        'username' => 'plaid_test',
//                        'password' => 'plaid_good',
                    ),
//                    'email' => 'email-test@plaid.com',
                    'type' => 'citi',
                    'options' => array(
                        'login' => true,
                    ),
                )
            ),
            'application/json'
        )
        ->expects('application/json');


        $response = $request->send();


        echo '<pre>';
        var_dump($request->serialized_payload);
        print_r($response->headers);
        print_r($response->body);
        print_r($response);
        echo '</pre>';
    }

    public function connectStep() {
exit;
        $request = Request::post(
            $this->getAppConfig()->getPlaidApiEndpoint() . 'connect/step',
            array_merge(
                $this->getAppConfig()->getPlaidApiCredentials(),
                array(
                    'access_token' => '',
                    'mfa' => '',
                )
            ),
            'application/json'
        )
        ->expects('application/json');


        $response = $request->send();


        echo '<pre>';
        var_dump($request->serialized_payload);
        print_r($response->headers);
        print_r($response->body);
        print_r($response);
        echo '</pre>';
    }
} 