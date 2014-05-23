<?php
require_once 'include.php';

use Barany\Core\Http\Kernel;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

$kernel = new Kernel(new \Config());
return ConsoleRunner::createHelperSet($kernel->getEntityManager());
