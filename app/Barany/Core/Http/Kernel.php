<?php
namespace Barany\Core\Http;

use Barany\Command;
use Barany\Core\AppConfig;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\Tools\Setup;

class Kernel implements Command {
    /**
     * @var AppConfig
     */
    private $appConfig;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(AppConfig $appConfig) {
        $this->appConfig = $appConfig;
        $this->router = new Router($this);
        $this->setupDoctrine();
    }

    private function setupDoctrine() {
        $paths = array(ROOT . '/app/Barany/Model');
//@todo: Set via config
$isDevMode = true;

        $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
        $config->setNamingStrategy(new UnderscoreNamingStrategy());
        $this->entityManager = EntityManager::create($this->appConfig->getDatabaseCredentials(), $config);
    }

    public function execute() {
        $this->router->dispatch();
    }

    public function getAppConfig() {
        return $this->appConfig;
    }

    public function getEntityManager() {
        return $this->entityManager;
    }
}
