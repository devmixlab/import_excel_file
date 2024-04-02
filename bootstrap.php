<?php

use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Cache\Adapter\Filesystem\FilesystemCachePool;
//use App\Database\DB;

//require __DIR__ . '/helpers.php';

//global $app;

//$container = $app->getContainer();
//$container->singleton(\App\Database\DBInterface::class, function (array $conn_parms = []) {
//    return new App\Database\DB($conn_parms);
//});

//$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
//$dotenv->load();

//ini_set('max_execution_time', '300');
set_time_limit(600);

const ROOT = __DIR__ . DIRECTORY_SEPARATOR;
const CACHE = ROOT . 'cache' . DIRECTORY_SEPARATOR;
const PUB = ROOT . 'public' . DIRECTORY_SEPARATOR;
const FILES = ROOT . 'files' . DIRECTORY_SEPARATOR;
const ROUTES = ROOT . 'routes' . DIRECTORY_SEPARATOR;
const RESOURCES = ROOT . 'resources' . DIRECTORY_SEPARATOR;
const VIEWS = RESOURCES . 'views' . DIRECTORY_SEPARATOR;
const SRC = ROOT . 'src' . DIRECTORY_SEPARATOR;
const CONTROLLERS = SRC . 'Controllers' . DIRECTORY_SEPARATOR;

//$twig = Twig::create(VIEWS, ['cache' => false]);
//$app->add(TwigMiddleware::create($app, $twig));
//
//
//$filesystemAdapter = new Local(CACHE . '/');
//$filesystem        = new Filesystem($filesystemAdapter);
//
//$pool = new FilesystemCachePool($filesystem);