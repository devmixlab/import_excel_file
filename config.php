<?php

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

return (function () {
    $pdo = function () {
        try {
//                $db_host = !empty($host) ? $_ENV['DB_HOST_FROM_CONSOLE'] : $_ENV['DB_HOST'];
            $pdo = new PDO("mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_DATABASE']}", $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);

            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

            return $pdo;
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    };

    $db = function ($c) {
        return new \App\Database\DB($c->get(PDO::class));
    };

    return [
        "container" => [
            "web" => [
                'settings' => [
//                'determineRouteBeforeAppMiddleware' => true,
//                'viewTemplatesDirectory' => '../templates',
//                'determineRouteBeforeAppMiddleware' => true
                ],
                PDO::class => $pdo,
                \App\Database\DBInterface::class => $db,
                \Slim\Views\Twig::class => function (\Psr\Container\ContainerInterface $c) {
                    return \Slim\Views\Twig::fromRequest($c->get(\Psr\Http\Message\RequestInterface::class));
                },
            ],
            "console" => [
                'settings' => [
//                'determineRouteBeforeAppMiddleware' => true,
//                'viewTemplatesDirectory' => '../templates',
//                'determineRouteBeforeAppMiddleware' => true
                ],
                PDO::class => $pdo,
                \App\Database\DBInterface::class => $db,
            ],
        ]
    ];
})();

//return [
//    "container" => [
//        "common" => [
//            'settings' => [
////                'determineRouteBeforeAppMiddleware' => true,
////                'viewTemplatesDirectory' => '../templates',
////                'determineRouteBeforeAppMiddleware' => true
//            ],
//            PDO::class => function () {
//                try {
//                    $db_host = !empty($_ENV['DB_HOST_FROM_CONSOLE']) ? $_ENV['DB_HOST_FROM_CONSOLE'] : $_ENV['DB_HOST'];
//                    $pdo = new PDO("mysql:host={$db_host};dbname={$_ENV['DB_DATABASE']}", $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
//
//                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//                    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
//
//                    return $pdo;
//                }
//                catch(PDOException $e)
//                {
//                    echo "Connection failed: " . $e->getMessage();
//                }
//            },
//            \App\Database\DBInterface::class => function ($c) {
//                return new \App\Database\DB($c->get(PDO::class));
//            },
//        ],
//        "web" => [
//            'settings' => [
////                'determineRouteBeforeAppMiddleware' => true,
////                'viewTemplatesDirectory' => '../templates',
////                'determineRouteBeforeAppMiddleware' => true
//            ],
////            \App\Database\DBInterface::class => function () {
////                return new \App\Database\DB();
////            },
//            \Slim\Views\Twig::class => function (\Psr\Container\ContainerInterface $c) {
////                dd($container->get(Request::class));
////                dd($container->get(RequestInterface::class));
////                exit;
//                return \Slim\Views\Twig::fromRequest($c->get(\Psr\Http\Message\RequestInterface::class));
//            },
//        ],
//        "web_test" => [
////            'settings' => [
//////                'determineRouteBeforeAppMiddleware' => true,
//////                'viewTemplatesDirectory' => '../templates',
//////                'determineRouteBeforeAppMiddleware' => true
////            ],
////            \App\Database\DBInterface::class => function () {
////                return new \App\Database\DB([
////                    "host" => "127.0.0.1"
////                ]);
////            },
//            \Slim\Views\Twig::class => function (\Psr\Container\ContainerInterface $c) {
////                dd($container->get(Request::class));
////                dd($container->get(RequestInterface::class));
////                exit;
//                return \Slim\Views\Twig::fromRequest($c->get(\Psr\Http\Message\RequestInterface::class));
//            },
//        ],
//        "console" => [
//            'settings' => [
////                'determineRouteBeforeAppMiddleware' => true,
////                'viewTemplatesDirectory' => '../templates',
////                'determineRouteBeforeAppMiddleware' => true
//            ],
//            PDO::class => function () {
//                try {
//                    $pdo = new PDO("mysql:host={$_ENV['DB_HOST_FROM_CONSOLE']};dbname={$_ENV['DB_DATABASE']}", $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
//
//                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//                    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
//
//                    return $pdo;
//                }
//                catch(PDOException $e)
//                {
//                    echo "Connection failed: " . $e->getMessage();
//                }
//            },
//            \App\Database\DBInterface::class => function ($c) {
//                return new \App\Database\DB($c->get(PDO::class));
//            },
//        ]
//    ]
//];