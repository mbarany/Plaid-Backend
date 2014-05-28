<?php
namespace Barany\Core;

use Barany\Model\Exportable;
use Barany\Core\Http\Kernel;
use Doctrine\Common\Collections\Collection;

class AppController {
    /**
     * @var Kernel
     */
    private $kernel;

    private $view;

    public function __construct(Kernel $kernel, $view) {
        $this->kernel = $kernel;
        $this->view = new View($view);
    }

    protected function getAppConfig() {
        return $this->kernel->getAppConfig();
    }

    protected function getEntityManager() {
        return $this->kernel->getEntityManager();
    }

    protected function getSession() {
        return $this->kernel->getSession();
    }

    protected function render() {
        $this->view->render();
    }

    /**
     * @param mixed $data
     */
    protected function renderJson($data = null) {
        header('Content-Type: application/json');
        if (null === $data) {
            return;
        }
        if (!is_array($data) && !$data instanceof Collection) {
            echo json_encode($data);
            return;
        }
        $exportedData = [];
        foreach ($data as $k => $v) {
            $exportedData[$k] = $v instanceof Exportable ? $v->toApi() : $v;
        }
        echo json_encode($exportedData);
    }

    protected function redirect($url) {
        if (strpos($url, '/') === 0) {
            $url = 'http://' . $_SERVER['HTTP_HOST'] . $url;
        }
        header('Location: ' . $url);
    }
} 