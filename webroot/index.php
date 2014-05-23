<?php
require_once '../include.php';

use Barany\Core\Http\Kernel;

$kernel = new Kernel(new \Config());
$kernel->execute();
