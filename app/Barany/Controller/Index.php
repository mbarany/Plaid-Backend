<?php
namespace Barany\Controller;

use Barany\Core\AppController;
use Httpful\Request;

class Index extends AppController {
    public function index() {
        $this->render();
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