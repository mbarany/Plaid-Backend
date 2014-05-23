<?php
namespace Barany\Core\Http;

use Barany\Core\AppController;
use Klein\Klein;

class Router {
    /**
     * @var \Klein\Klein
     */
    private $klein;

    /**
     * @var Kernel
     */
    private $kernel;

    public function __construct(Kernel $kernel) {
        $this->kernel = $kernel;
        $this->klein = new Klein();
        $this->load();
    }

    public function dispatch() {
        $this->klein->dispatch();
    }

    /**
     * @todo: don't eagerly create a new object for each route
     */
    private function load() {
        $this->klein->get('/', $this->toCallable('Index', 'index'));
//        $this->klein->get('/connect', $this->toCallable('Index', 'connect'));
//        $this->klein->get('/connect/step', $this->toCallable('Index', 'connectStep'));

        $this->klein->get('/api/institutions', $this->toCallable('Api', 'institutions'));
        $this->klein->get('/api/accounts', $this->toCallable('Api', 'accounts'));
        $this->klein->get('/api/account/[:account_id]', $this->toCallable('Api', 'account'));
    }

    private function toCallable($controller, $action) {
        $class = '\Barany\Controller\\' . $controller;
        /** @var AppController $controllerObject */
        $controllerObject = new $class($this->kernel, $controller . '/' . $action);
        return array($controllerObject, $action);
    }
} 