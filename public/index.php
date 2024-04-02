<?php
require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/bootstrap.php';

use App\App\WebApp;
//use App\Facades\App;

error_reporting(E_ALL ^ E_DEPRECATED);

//(new WebApp())->initWeb();
(new WebApp())->run();

//dd(AppFacade::test());

//use Slim\Factory\AppFactory;
//use App\FileImporter\Importer;
//
//$app = AppFactory::create();
//
//require dirname(__DIR__) . '/bootstrap.php';
////require dirname(__DIR__) . '/helpers.php';
////require dirname(__DIR__) . '/routes/web.php';
//
//$app->run();