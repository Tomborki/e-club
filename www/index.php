<?php

session_start();

require '../vendor/autoload.php';
// include page config
require_once ('../app/Config/config.inc.php');

// include autoloader
require_once ('autoloader.php');

$router = new RouterController();
$router->zpracuj(array($_SERVER['REQUEST_URI']));
$router->showView();

