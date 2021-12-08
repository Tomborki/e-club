<?php

if( !session_id() ) @session_start();

require '../vendor/autoload.php';
// include page config
require_once ('../app/Config/config.inc.php');

// include autoloader
require_once ('autoloader.php');

$router = new RouterController();
$router->zpracuj(array($_SERVER['REQUEST_URI']));
//$router->showView();

/*
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 600)) {
    $router->redirect('login');
}
$_SESSION['LAST_ACTIVITY'] = time();
*/

