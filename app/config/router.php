<?php

use Phalcon\Mvc\Router;

// $router = $di->getRouter();
$router = new Router();



$router->handle($_SERVER['REQUEST_URI']);
